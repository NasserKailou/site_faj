<?php
/**
 * Chargeur d'environnement léger (.env) — FAJ Niger
 * ---------------------------------------------------------------------------
 * Respecte l'architecture existante (PHP pur, aucune dépendance externe).
 * Charge le fichier .env à la racine du projet et expose ses valeurs via
 * env('CLE', 'defaut'). Les valeurs sont injectées dans $_ENV / getenv().
 *
 * Le fichier .env NE DOIT JAMAIS être committé (voir .gitignore).
 * Utilisez .env.example comme modèle.
 *
 * @package FAJ\Includes
 */

if (!function_exists('faj_load_env')) {
    /**
     * Charge un fichier .env dans $_ENV et l'environnement du processus.
     * Ne remplace pas les variables déjà définies dans l'environnement réel.
     */
    function faj_load_env(string $path): void
    {
        static $loaded = [];
        if (isset($loaded[$path])) {
            return;
        }
        $loaded[$path] = true;

        if (!is_file($path) || !is_readable($path)) {
            return; // Aucun .env : on retombe sur les valeurs par défaut.
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignorer commentaires et lignes vides
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            // Format attendu : CLE=valeur (support "export CLE=valeur")
            if (strncmp($line, 'export ', 7) === 0) {
                $line = substr($line, 7);
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key   = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            if ($key === '') {
                continue;
            }

            // Retirer les guillemets encadrants éventuels
            $len = strlen($value);
            if ($len >= 2) {
                $first = $value[0];
                $last  = $value[$len - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                    // Décoder les échappements simples pour les guillemets doubles
                    if ($first === '"') {
                        $value = str_replace(['\\n', '\\r', '\\t', '\\"'], ["\n", "\r", "\t", '"'], $value);
                    }
                }
            }

            // Ne pas écraser une variable réellement définie dans l'environnement
            if (getenv($key) !== false || array_key_exists($key, $_ENV)) {
                continue;
            }

            putenv("$key=$value");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

if (!function_exists('env')) {
    /**
     * Récupère une variable d'environnement avec valeur par défaut.
     * Convertit automatiquement les booléens/null textuels.
     *
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }

        switch (strtolower((string) $value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }

        return $value;
    }
}

// Chargement automatique du .env situé à la racine du projet.
faj_load_env(dirname(__DIR__) . '/.env');
