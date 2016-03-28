<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller
{
    protected $ci_key = array(
        'controller' => 'controllers',
        'model' =>'models',
        'library' =>'libraries'
    );

    public function __construct()
    {
        parent::__construct();

        // Can be called only from the terminal
        if (!$this->input->is_cli_request()) {
            exit('Direct access denied! Use terminal.');
        }

        $this->load->dbforge(); 
        $this->load->library('migration');      
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
     * @params $file_type string
     * @params $title string  
     */
    public function create($file_type, $title)
    {
        $row = null;

        if ($this->ci_key[$file_type]) {
            $row = $this->ci_key[$file_type] . '/' . $title;
        }
        else {
            unset($row); 
        }

        // Check file type & similar file
        if (!isset($row)) {
            exit('Error: "' . $file_type . '" is not available file type!');
        }
        elseif (file_exists(APPPATH . $row .'.php')) {
            exit('Error: "' . $row . '.php" is already exist!' . PHP_EOL);
        }

        $path = APPPATH . $row . '.php';
        $template = $this->load->view('tools/' . $this->ci_key[$file_type], $data['name'] = $title, TRUE);

        // Create file
        $file = fopen($path, "w") or die('Error: unable to create file!' . PHP_EOL);
        fwrite($file, $template);
        fclose($file);

        echo 'Success: "'.$row.'.php" has been created.' . PHP_EOL;
    }

    /**
     * Delete application files.
     * @params $file_type string
     * @params $title string  
     */
    public function delete($file_type, $title)
    {
        $row = null;

        if ($this->ci_key[$file_type]) {
            $row = $this->ci_key[$file_type] . '/' . $title;
        }
        else {
            unset($row); 
        }

        // Check file type & similar file
        if (!isset($row)) {
            exit('Error: "' . $file_type . '" is not available file type!' . PHP_EOL);
        }
        elseif (file_exists(APPPATH . $row . '.php')) {
            $file = APPPATH . $row . '.php';
            // Delete file
            unlink($file) or die('Error: unable to delete file!' . PHP_EOL);
            echo 'Success: "' . $row . '.php" has been deleted.' . PHP_EOL;
        }
        else {
            exit('Error: unable to delete file!' . PHP_EOL);
        }  
    }    
}