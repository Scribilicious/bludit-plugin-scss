<?php
use \ScssPhp\ScssPhp\Compiler;

class pluginScss extends Plugin {

    private $tabs = null;

    /**
     * Initialize
     */
    public function init()
    {
        $this->dbFields = [
            'files' => '',
            'auto-compile' => null,
        ];
    }

    public function beforeSiteLoad()
    {
        if ($this->getValue('auto-compile')) {
            $this->compile();
        }
    }

    /**
     * Check before saving
     */
    public function post()
    {
        // Gets the files
        $paths = $this->getPaths();

        foreach($paths as $key => $path) {
            $paths[$key] = $this->createEntry($_POST['source_' . $key], $_POST['target_' . $key]);
        }

        // Force compile
        if (isset($_POST['compile'])) {
            $this->compile();
        }

        // Adds a file
        if (isset($_POST['add']) && !empty($_POST['source']) && !empty($_POST['target'])) {
            $paths[] = $this->createEntry($_POST['source'], $_POST['target']);
        }

        // Remove an entry
        if (isset($_POST['delete'])) {
            $key = intval($_POST['delete']);
            unset($paths[$key]);
        }

        // Sets the fields to save
        $this->db['auto-compile'] = intval($_POST['auto-compile']);
        $this->db['files'] = !empty($paths) ? json_encode(array_values($paths)) : '';

        // Save the database
        return $this->save();
    }

    /**
     * Creates the config form
     */
    public function form()
    {
        global $L;

        $html = $this->payMe();

        $html .= '<div class="alert alert-primary" role="alert">';
        $html .= $L->get('scss Help');
        $html .= '</div>';

        $html .= '<h4 class="mt-3">'.$L->get('scss Settings').'</h4>';
        $html .= '<div>';
        $html .= '<label>' . $L->get('scss Develop mode') . '</label>';
        $html .= '<select name="auto-compile">';
        $html .= '<option value=""'.($this->getValue('auto-compile') === 0 ? ' selected' : '').'>' . $L->get('scss Deactivated') . '</option>';
        $html .= '<option value="1"'.($this->getValue('auto-compile') === 1 ? ' selected' : '').'>' . $L->get('scss Activated') . '</option>';
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<button name="compile" class="btn btn-primary my-2" type="submit">'.$L->get('scss Compile').'</button>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>'.$L->get('scss Source').'</label>';
        $html .= '<input name="source" type="text" class="form-control" value="" placeholder="'.$L->get('scss Source').'">';
        $html .= '<span class="tip">'.$L->get('scss Enter source.').'</span>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>'.$L->get('scss Target').'</label>';
        $html .= '<input name="target" type="text" class="form-control" value="" placeholder="'.$L->get('scss Target').'">';
        $html .= '<span class="tip">'.$L->get('scss Enter target.').'</span>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<button name="add" class="btn btn-primary my-2" type="submit">'.$L->get('scss Add').'</button>';
        $html .= '</div>';

        // Get the JSON DB, getValue() with the option unsanitized HTML code
        $paths = $this->getPaths();

        if (!empty($paths)) {
            $html .= '<hr><h4 class="mt-3">'.$L->get('scss Paths').'</h4>';

            foreach($paths as $key => $path) {
                $html .= '<div>';
                $html .= '<label>'.$L->get('scss Source').'</label>';
                $html .= '<input type="text" name="source_'.$key.'" class="form-control" value="'.$path['source']['path'].'">';
                $html .= ($path['source']['status'] ? '' : '<span class="tip">'.$L->get(ucfirst($path['source']['type']) . ' error.').'</span>');
                $html .= '</div>';

                $html .= '<div>';
                $html .= '<label>'.$L->get('scss Target').'</label>';
                $html .= '<input type="text" name="target_'.$key.'" class="form-control" value="'.$path['target']['path'].'">';
                $html .= ($path['target']['status'] ? '' : '<span class="tip">'.$L->get('scss Folder error.').'</span>');
                $html .= '</div>';

                $html .= '<div>';
                $html .= '<button name="delete" class="btn btn-secondary my-2" type="submit" value="'.$key.'">'.$L->get('scss Delete').'</button>';
                $html .= '</div><hr>';
            }
        }

        $html .= '<div>';
        $html .= '<button name="add" class="btn btn-primary my-2" type="submit">'.$L->get('scss Save').'</button>';
        $html .= '</div>';

        $html .= $this->footer();

        return $html;
    }

    /**
     * Creates a entry for the json data
     */
    private function createEntry($source, $target) {
        $source = trim(trim(filter_var($source), FILTER_SANITIZE_URL), '/');
        $target = trim(trim(filter_var($target), FILTER_SANITIZE_URL), '/');

        return [
            'source' => [
                'path' => $source,
                'type' => is_dir(PATH_ROOT . $source) ? 'path' : 'file',
                'status' => is_dir(PATH_ROOT . $source) || file_exists(PATH_ROOT . $source),
            ],
            'target' => [
                'path' => $target,
                'status' => is_dir(PATH_ROOT . $target),
            ]
        ];
    }

    /**
     * Creates the Support Me Button...
     */
    private function payMe() {
        global $L;

        $icons = ['💸', '🥹', '☕️', '🍻', '👾', '🍕'];
        shuffle($icons);
        $html = '<div class="bg-light text-center border mt-3 p-3">';
        $html .= '<p class="mb-2">' . $L->get('Please support Mr.Bot') . '</p>';
        $html .= '<a style="background: #ffd11b;box-shadow: 2px 2px 5px #ccc;padding: 0 10px;border-radius: 50%;width: 60px;display: block;text-align: center;margin: auto;height: 60px; font-size: 40px; line-height: 60px;" href="https://www.buymeacoffee.com/iambot" target="_blank" title="Buy me a coffee...">' . $icons[0] . '</a>';
        $html .= '</div><br>';

        return $html;
    }

    /**
     * Creates the Footer
     */
    private function footer() {
        $html = '<div class="text-center mt-3 p-3" style="opacity: 0.6;">';
        $html .= '<p class="mb-2">© ' . date('Y') . ' by <a href="https://github.com/Scribilicious" target="_blank" title="Visit GitHub page...">Mr.Bot</a>, Licensed under <a href="https://raw.githubusercontent.com/Scribilicious/MIT/main/LICENSE" target="_blank" title="view license...">MIT</a>.</p>';
        $html .= '</div><br>';

        return $html;
    }

    /**
     * Get the paths
     */
    private function getPaths() {
        return json_decode($this->getValue('files', $unsanitized=false), true) ?: [];
    }

    /**
     * Runs the compiler
     */
    private function compile() {
        $paths = $this->getPaths();

        if (empty($paths)) {
            return;
        }

        include_once $this->phpPath() . 'vendor/autoload.php';

        $scss = new Compiler();

        foreach($paths as $path) {
            if ($path['target']['status'] !== true || $path['source']['status'] !== true) {
                continue;
            }

            if ($path['source']['type'] === 'path') {
                $sources = glob(PATH_ROOT. $path['source']['path'] . '/*.scss');
            } else {
                $sources[] = PATH_ROOT. $path['source']['path'];
            }
            $target = PATH_ROOT. $path['target']['path'];

            foreach($sources as $source) {
                $source = $source;
                $pathinfoSource = pathinfo($source);

                $scss->setSourceMap(\ScssPhp\ScssPhp\Compiler::SOURCE_MAP_FILE);
                $scss->setOutputStyle('compressed');
                $scss->setImportPaths($pathinfoSource['dirname']);

                $scss->setSourceMapOptions([
                    'sourceMapURL' => $pathinfoSource['filename'] . '.map',
                    'sourceMapBasepath' => $target,
                    'sourceMapFilename' => $pathinfoSource['filename'] . '.css',
                ]);

                $output = $scss->compileString('@import "' . $pathinfoSource['basename'] . '";');

                $css = $output->getCss();
                $filename = $target . '/' . $pathinfoSource['filename'];

                if (@file_get_contents($filename . '.css') !== $css) {
                    file_put_contents($filename . '.css', $css);
                    file_put_contents($filename . '.map', $output->getSourceMap());
                }
            }
        }
    }
}
