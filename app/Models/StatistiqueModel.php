<?php

namespace App\Models;

use App\Core\Database;
use PDO;

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

    public static function journalPourMatch(int $matchId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT al.*, j.nom AS joueur_nom, j.numero AS joueur_numero
             FROM actions_log al
             LEFT JOIN joueurs j ON j.id = al.joueur_id
             WHERE al.match_id = :match_id
             ORDER BY al.quart_temps, al.id"
        );
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetchAll();
    }

    public static function sauvegarderLot(int $matchId, array $actions): void
    {
        if (empty($actions)) {
            return;
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmtLog = $pdo->prepare(
                "INSERT INTO actions_log (match_id, joueur_id, quart_temps, type, reussi, type_possession, valeur_temps)
                 VALUES (:match_id, :joueur_id, :quart_temps, :type, :reussi, :type_possession, :valeur_temps)"
            );

            foreach ($actions as $a) {
                $stmtLog->execute([
                    'match_id' => $matchId,
                    'joueur_id' => isset($a['joueur_id']) && $a['joueur_id'] !== null ? (int) $a['joueur_id'] : null,
                    'quart_temps' => (int) $a['quart_temps'],
                    'type' => $a['type'],
                    'reussi' => isset($a['reussi']) ? ($a['reussi'] ? 1 : 0) : null,
                    'type_possession' => $a['type_possession'] ?? null,
                    'valeur_temps' => isset($a['valeur_temps']) && $a['valeur_temps'] !== null ? (int) $a['valeur_temps'] : null,
                ]);
            }

            self::recalculerAgregats($pdo, $matchId);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function supprimerAction(int $actionId, int $userId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT al.match_id FROM actions_log al
             JOIN matches m ON m.id = al.match_id
             WHERE al.id = :action_id AND m.user_id = :user_id"
        );
        $stmt->execute(['action_id' => $actionId, 'user_id' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $matchId = (int) $row['match_id'];

        $pdo->beginTransaction();

        try {
            $pdo->prepare("DELETE FROM actions_log WHERE id = :id")->execute(['id' => $actionId]);
            self::recalculerAgregats($pdo, $matchId);
            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function recalculerAgregats(PDO $pdo, int $matchId): void
    {
        $stmt = $pdo->prepare("SELECT * FROM actions_log WHERE match_id = :match_id ORDER BY id");
        $stmt->execute(['match_id' => $matchId]);
        $toutesActions = $stmt->fetchAll();

        ['individuel' => $individuel, 'collectif' => $collectif] = self::agregerActions($toutesActions);

        $pdo->prepare("DELETE FROM statistiques WHERE match_id = :id")->execute(['id' => $matchId]);
        $pdo->prepare("DELETE FROM statistiques_collectives WHERE match_id = :id")->execute(['id' => $matchId]);

        $stmtJoueur = $pdo->prepare(
            "INSERT INTO statistiques
                (match_id, joueur_id, tirs_2pts_tentes, tirs_2pts_reussis, tirs_3pts_tentes, tirs_3pts_reussis,
                 lancers_francs_tentes, lancers_francs_reussis, passes_decisives, duels_defensifs_gagnes, minutes_jouees)
             VALUES (:match_id, :joueur_id, :t2t, :t2r, :t3t, :t3r, :lft, :lfr, :pd, :ddg, :mj)"
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
                'mj' => $s['minutes_jouees'],
            ]);
        }

        $stmtCollectif = $pdo->prepare(
            "INSERT INTO statistiques_collectives
                (match_id, quart_temps, points, points_transition, points_jeu_pose, points_contre_attaque,
                 nb_possessions, possessions_transition, possessions_jeu_pose,
                 nb_contre_attaques, nb_contre_attaques_reussies,
                 lancers_francs_tentes, lancers_francs_reussis,
                 rebonds_defensifs, rebonds_offensifs_adversaires)
             VALUES (:match_id, :quart, :points, :pt, :pjp, :pca, :nbp, :post, :posjp, :nca, :ncar, :lft, :lfr, :rd, :roa)"
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
    }

    /**
     * Pure fonction d'agrégation. Les actions doivent être fournies triées par
     * ordre chronologique (id croissant) pour que l'appariement entrée/sortie
     * du temps de jeu fonctionne correctement.
     */
    private static function agregerActions(array $actions): array
    {
        $champsIndividuels = [
            'tirs_2pts_tentes', 'tirs_2pts_reussis',
            'tirs_3pts_tentes', 'tirs_3pts_reussis',
            'lancers_francs_tentes', 'lancers_francs_reussis',
            'passes_decisives', 'duels_defensifs_gagnes', 'minutes_jouees',
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
        $entreesEnCours = []; // joueur_id => minute d'entrée, en attente d'une sortie

        foreach ($actions as $a) {
            $quart = (int) $a['quart_temps'];
            $type = $a['type'];
            $reussi = (bool) ($a['reussi'] ?? false);
            $typePossession = $a['type_possession'] ?? null;
            $joueurId = isset($a['joueur_id']) && $a['joueur_id'] !== null ? (int) $a['joueur_id'] : null;
            $valeurTemps = isset($a['valeur_temps']) && $a['valeur_temps'] !== null ? (int) $a['valeur_temps'] : null;

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
            } elseif ($type === 'entree') {
                if ($joueurId !== null && $valeurTemps !== null) {
                    $entreesEnCours[$joueurId] = $valeurTemps;
                }
            } elseif ($type === 'sortie') {
                if ($joueurId !== null && $valeurTemps !== null && isset($entreesEnCours[$joueurId])) {
                    $duree = $valeurTemps - $entreesEnCours[$joueurId];
                    if ($duree > 0) {
                        $individuel[$joueurId] ??= array_fill_keys($champsIndividuels, 0);
                        $individuel[$joueurId]['minutes_jouees'] += $duree;
                    }
                    unset($entreesEnCours[$joueurId]);
                }
            }
        }

        return ['individuel' => $individuel, 'collectif' => $collectif];
    }
}