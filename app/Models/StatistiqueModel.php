<?php

namespace App\Models;

use App\Core\Database;

class StatistiqueModel
{
    public static function allPourMatch(int $matchId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT *, (tirs_2pts_reussis*2 + tirs_3pts_reussis*3 + lancers_francs_reussis) AS points
             FROM statistiques WHERE match_id = :match_id"
        );
        $stmt->execute(['match_id' => $matchId]);

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['joueur_id']] = $row;
        }
        return $result;
    }

    /**
     * Reçoit le journal complet d'actions d'un match et les agrège en une seule
     * écriture par joueur et par quart-temps.
     *
     * Types d'action possibles :
     *  - '2pts' / '3pts' / 'lf'  : tir, lié à un joueur. Les tirs 2/3pts utilisent
     *    type_possession UNIQUEMENT pour attribuer les points marqués (points_transition/
     *    points_jeu_pose/points_contre_attaque) — ils n'incrémentent PAS nb_possessions,
     *    car une possession peut contenir plusieurs tentatives (rebond offensif, etc.)
     *  - 'possession'             : déclare explicitement le début d'une possession
     *    (avec son type). C'est cette action, et elle seule, qui incrémente nb_possessions
     *    et le compteur du type correspondant.
     *  - 'passe' / 'duel'         : liés à un joueur uniquement (individuel)
     *  - 'rebond_def' / 'rebond_off_adv' : stats d'équipe par quart-temps, pas de joueur
     */
    public static function sauvegarderLot(int $matchId, array $actions): void
    {
        $champsIndividuels = [
            'tirs_2pts_tentes', 'tirs_2pts_reussis',
            'tirs_3pts_tentes', 'tirs_3pts_reussis',
            'lancers_francs_tentes', 'lancers_francs_reussis',
            'passes_decisives', 'duels_defensifs_gagnes',
        ];
        $champsCollectifs = [
            'points', 'points_transition', 'points_jeu_pose', 'points_contre_attaque',
            'nb_possessions', 'possessions_transition', 'possessions_jeu_pose',
            'nb_contre_attaques', 'nb_contre_attaques_reussies',
            'lancers_francs_tentes', 'lancers_francs_reussis',
            'rebonds_defensifs', 'rebonds_offensifs_adversaires',
        ];

        $individuel = [];
        $collectif = [];

        foreach ($actions as $a) {
            $quart = (int) $a['quart_temps'];
            $type = $a['type'];
            $reussi = (bool) ($a['reussi'] ?? false);
            $typePossession = $a['type_possession'] ?? null;
            $joueurId = isset($a['joueur_id']) && $a['joueur_id'] !== null ? (int) $a['joueur_id'] : null;

            $collectif[$quart] ??= array_fill_keys($champsCollectifs, 0);

            if ($type === '2pts' || $type === '3pts') {
                $prefixe = $type === '2pts' ? 'tirs_2pts' : 'tirs_3pts';
                $points = $type === '2pts' ? 2 : 3;

                $individuel[$joueurId] ??= array_fill_keys($champsIndividuels, 0);
                $individuel[$joueurId]["{$prefixe}_tentes"]++;

                if ($reussi) {
                    $individuel[$joueurId]["{$prefixe}_reussis"]++;
                    $collectif[$quart]['points'] += $points;

                    if ($typePossession === 'transition') {
                        $collectif[$quart]['points_transition'] += $points;
                    } elseif ($typePossession === 'jeu_pose') {
                        $collectif[$quart]['points_jeu_pose'] += $points;
                    } elseif ($typePossession === 'contre_attaque') {
                        $collectif[$quart]['points_contre_attaque'] += $points;
                        $collectif[$quart]['nb_contre_attaques_reussies']++;
                    }
                }
            } elseif ($type === 'possession') {
                $collectif[$quart]['nb_possessions']++;

                if ($typePossession === 'transition') {
                    $collectif[$quart]['possessions_transition']++;
                } elseif ($typePossession === 'jeu_pose') {
                    $collectif[$quart]['possessions_jeu_pose']++;
                } elseif ($typePossession === 'contre_attaque') {
                    $collectif[$quart]['nb_contre_attaques']++;
                }
            } elseif ($type === 'lf') {
                $individuel[$joueurId] ??= array_fill_keys($champsIndividuels, 0);
                $individuel[$joueurId]['lancers_francs_tentes']++;
                $collectif[$quart]['lancers_francs_tentes']++;

                if ($reussi) {
                    $individuel[$joueurId]['lancers_francs_reussis']++;
                    $collectif[$quart]['lancers_francs_reussis']++;
                    $collectif[$quart]['points'] += 1;
                }
            } elseif ($type === 'passe') {
                $individuel[$joueurId] ??= array_fill_keys($champsIndividuels, 0);
                $individuel[$joueurId]['passes_decisives']++;
            } elseif ($type === 'duel') {
                $individuel[$joueurId] ??= array_fill_keys($champsIndividuels, 0);
                $individuel[$joueurId]['duels_defensifs_gagnes']++;
            } elseif ($type === 'rebond_def') {
                $collectif[$quart]['rebonds_defensifs']++;
            } elseif ($type === 'rebond_off_adv') {
                $collectif[$quart]['rebonds_offensifs_adversaires']++;
            }
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmtJoueur = $pdo->prepare(
                "INSERT INTO statistiques
                    (match_id, joueur_id, tirs_2pts_tentes, tirs_2pts_reussis, tirs_3pts_tentes, tirs_3pts_reussis,
                     lancers_francs_tentes, lancers_francs_reussis, passes_decisives, duels_defensifs_gagnes)
                 VALUES (:match_id, :joueur_id, :t2t, :t2r, :t3t, :t3r, :lft, :lfr, :pd, :ddg)
                 ON DUPLICATE KEY UPDATE
                    tirs_2pts_tentes = tirs_2pts_tentes + :t2t,
                    tirs_2pts_reussis = tirs_2pts_reussis + :t2r,
                    tirs_3pts_tentes = tirs_3pts_tentes + :t3t,
                    tirs_3pts_reussis = tirs_3pts_reussis + :t3r,
                    lancers_francs_tentes = lancers_francs_tentes + :lft,
                    lancers_francs_reussis = lancers_francs_reussis + :lfr,
                    passes_decisives = passes_decisives + :pd,
                    duels_defensifs_gagnes = duels_defensifs_gagnes + :ddg"
            );

            foreach ($individuel as $joueurId => $s) {
                if ($joueurId === null) {
                    continue;
                }
                $stmtJoueur->execute([
                    'match_id' => $matchId, 'joueur_id' => $joueurId,
                    't2t' => $s['tirs_2pts_tentes'], 't2r' => $s['tirs_2pts_reussis'],
                    't3t' => $s['tirs_3pts_tentes'], 't3r' => $s['tirs_3pts_reussis'],
                    'lft' => $s['lancers_francs_tentes'], 'lfr' => $s['lancers_francs_reussis'],
                    'pd' => $s['passes_decisives'], 'ddg' => $s['duels_defensifs_gagnes'],
                ]);
            }

            $stmtCollectif = $pdo->prepare(
                "INSERT INTO statistiques_collectives
                    (match_id, quart_temps, points, points_transition, points_jeu_pose, points_contre_attaque,
                     nb_possessions, possessions_transition, possessions_jeu_pose,
                     nb_contre_attaques, nb_contre_attaques_reussies,
                     lancers_francs_tentes, lancers_francs_reussis,
                     rebonds_defensifs, rebonds_offensifs_adversaires)
                 VALUES (:match_id, :quart, :points, :pt, :pjp, :pca, :nbp, :post, :posjp, :nca, :ncar, :lft, :lfr, :rd, :roa)
                 ON DUPLICATE KEY UPDATE
                    points = points + :points,
                    points_transition = points_transition + :pt,
                    points_jeu_pose = points_jeu_pose + :pjp,
                    points_contre_attaque = points_contre_attaque + :pca,
                    nb_possessions = nb_possessions + :nbp,
                    possessions_transition = possessions_transition + :post,
                    possessions_jeu_pose = possessions_jeu_pose + :posjp,
                    nb_contre_attaques = nb_contre_attaques + :nca,
                    nb_contre_attaques_reussies = nb_contre_attaques_reussies + :ncar,
                    lancers_francs_tentes = lancers_francs_tentes + :lft,
                    lancers_francs_reussis = lancers_francs_reussis + :lfr,
                    rebonds_defensifs = rebonds_defensifs + :rd,
                    rebonds_offensifs_adversaires = rebonds_offensifs_adversaires + :roa"
            );

            foreach ($collectif as $quart => $s) {
                $stmtCollectif->execute([
                    'match_id' => $matchId, 'quart' => $quart,
                    'points' => $s['points'], 'pt' => $s['points_transition'], 'pjp' => $s['points_jeu_pose'],
                    'pca' => $s['points_contre_attaque'],
                    'nbp' => $s['nb_possessions'], 'post' => $s['possessions_transition'], 'posjp' => $s['possessions_jeu_pose'],
                    'nca' => $s['nb_contre_attaques'], 'ncar' => $s['nb_contre_attaques_reussies'],
                    'lft' => $s['lancers_francs_tentes'], 'lfr' => $s['lancers_francs_reussis'],
                    'rd' => $s['rebonds_defensifs'], 'roa' => $s['rebonds_offensifs_adversaires'],
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}