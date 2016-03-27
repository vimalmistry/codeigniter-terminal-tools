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
        $info = "Available commands through \"php index.php\":\n\n";
        $info .= "tools migration \"file_name\"         Create new migration file\n";
        $info .= "tools migrate \"version_number\"      Run all migrations. The version number is optional.\n";
        $info .= "tools reset                         Reset all migrations.\n";

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
                echo 'Success: migration has been launched.';
            }
            else {
                show_error($this->migration->error_string());                
            }
        }
        else {
            if ($this->migration->latest()) {
                echo 'Success: migrations has been launched.';
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
        $migration_file = APPPATH . 'migrations/'. date('YmdHis') . '_' . $name .'.php';
        $migration_template = $this->load->view('tools/migrations', $data, TRUE);

        if (write_file($migration_file, $migration_template, "w")) {
            echo 'Success: migration file has been created.';
        }
        else {
            echo 'Error: unable to create migration file!';
        }
    }
    /**
     * Reset all migrations from database.
     */    
    public function reset()
    {
        $this->migration->version(0);
        echo 'Success: migrations has been reseted.';            
    }  
}
