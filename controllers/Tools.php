<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Can be called only from the terminal
        if (!$this->input->is_cli_request()) {
            exit('Direct access denied! Use terminal.');
        }

        $this->load->dbforge(); 
        $this->load->library('migration');
        $this->load->helper('file');       
    }

    /**
     * Display the help menu
     * @print shows available actions
     */
    public function help() {
        $info = "CI migration commands:\n";
        $info .= "php index.php tools migration \"name\"            Create new migration file\n";
        $info .= "php index.php tools migrate \"version\"           Run all migrations. The version number is optional.\n";
        $info .= "php index.php tools reset                       Reset all migrations.\n\n";
        $info .= "CI file commands:\n";        
        $info .= "php index.php tools create \"file\" \"name\"        Create new file.\n";
        $info .= "php index.php tools delete \"file\" \"name\"        Delete file.";

        print $info . PHP_EOL;
    }

    /**
     * Run all pending migration files.
     * The migration file number is optional. It's useful for rolling back migrations.
     * @params $number int
     */
    public function migrate($number = null)
    {
        if ($number) {
            if ($this->migration->version($number)) {
                echo 'Success: migration has been launched.' . PHP_EOL;
            }
            else {
                show_error($this->migration->error_string());                
            }
        }
        else {
            if ($this->migration->latest()) {
                echo 'Success: migrations has been launched.' . PHP_EOL;
            }
            else {
                show_error($this->migration->error_string());
            }            
        }
    }

    /**
     * Create a migration file.
     * @params $name string
     */    
    public function migration($name)
    {
        $data['name'] = strtolower($name);

        $path = APPPATH . 'migrations/'. date('YmdHis') . '_' . $data['name'] .'.php';
        $migration_template = $this->load->view('tools/migrations', $data, TRUE);

        $migration = fopen($path, "w") or die('Error: unable to create migration file!' . PHP_EOL);
        fwrite($migration, $migration_template);
        fclose($migration);

        echo 'Success: migration file has been created.' . PHP_EOL;
    }

    /**
     * Reset all migrations from database.
     */    
    public function reset()
    {
        $this->migration->version(0);
        echo 'Success: migrations has been reseted.' . PHP_EOL;            
    }

    /**
     * Create application files.
     * Available file types: [controller, model, library]
     * @params $key string
     * @params $value string  
     */
    public function create($key, $value)
    {
        $data = array();

        switch ($key) {
            case 'controller':
                $data = array('file' => 'controllers', 'name' => $value);
                break;
            case 'model':
                $data = array('file' => 'models', 'name' => $value);
                break;
            case 'library':
                $data = array('file' => 'libraries', 'name' => $value);
                break;
            default:
                unset($data);                
        }

        // Check file type & similar file
        if (!isset($data)) {
            exit('Error: "'.$key.'" is not available type!');
        }
        elseif (file_exists(APPPATH . $data['file'] .'/'. $data['name'] .'.php')) {
            exit('Error: "'.$data['file'] .'/'. $data['name'] .'.php" is already exist!' . PHP_EOL);
        }

        $path = APPPATH . $data['file'] .'/'. $data['name'] .'.php';
        $template = $this->load->view('tools/'.$data['file'], $data, TRUE);

        // Create file
        $file = fopen($path, "w") or die('Error: unable to create file!' . PHP_EOL);
        fwrite($file, $template);
        fclose($file);

        echo 'Success: "'. $data['file'] .'/'. $data['name'] .'.php" has been created.' . PHP_EOL;
    }

    /**
     * Delete application files.
     * Available file types: [controller, model, library]
     * @params $key string
     * @params $value string  
     */
    public function delete($key, $value)
    {
        $data = array();
        
        switch ($key) {
            case 'controller':
                $data = array('file' => 'controllers', 'name' => $value);
                break;
            case 'model':
                $data = array('file' => 'models', 'name' => $value);
                break;
            case 'library':
                $data = array('file' => 'libraries', 'name' => $value);
                break;
            default:
                unset($data);                
        }

        // Check file type & similar file
        if (!isset($data)) {
            exit('Error: "'.$key.'" is not available type!' . PHP_EOL);
        }
        elseif (file_exists(APPPATH . $data['file'] .'/'. $data['name'] .'.php')) {
            $file = APPPATH . $data['file'] .'/'. $data['name'] .'.php';
            // Delete file
            unlink($file) or die('Error: unable to delete file!' . PHP_EOL);
            echo 'Success: "'. $data['file'] .'/'. $data['name'] .'.php" has been deleted.' . PHP_EOL;
        }
        else {
            exit('Error: unable to delete file!' . PHP_EOL);
        }  
    }    
}
