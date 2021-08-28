<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\includes\Process;

class Cron{
    private $interval_cron;
    private $enable_enqueu;
    private $interval_remove_log;

    public function __construct(){

        $options = get_option(DCMS_ENQUEU_OPTIONS);
        $this->interval_cron = intval($options['dcms_cron_interval']);
        $this->enable_enqueu = $options['dcms_enable_queue'];
        $this->interval_remove_log = intval($options['dcms_remove_log']);

        add_filter( 'cron_schedules', [ $this, 'dcms_custom_schedule' ]);
        add_action( 'dcms_enqueu_hook', [ $this, 'dcms_cron_enqueu_process' ] );
        add_action( 'dcms_remove_log_hook', [ $this, 'dcms_cron_remove_process' ] );
    }

    // Add new schedule
    public function dcms_custom_schedule( $schedules ) {
        $schedules['dcms_enqueu_interval'] = array(
            'interval' => $this->interval_cron*60,
            'display' => ($this->interval_cron*60) . ' seconds'
        );

        $schedules['dcms_remove_log_interval'] = array(
            'interval' => $this->interval_remove_log*86400,
            'display' => ($this->interval_remove_log*86400) . ' seconds'
        );

        return $schedules;
    }

    // Cron process
    public function dcms_cron_enqueu_process() {
        $process = new Process();
        if ( $this->enable_enqueu ){
            $process->process_sent();
        }
    }

    public function dcms_cron_remove_process(){
        if ( $this->enable_enqueu ){
            error_log('Procesando en un día');
        }
    }
}