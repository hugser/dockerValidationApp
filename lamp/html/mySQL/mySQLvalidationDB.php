<?php



    /*-----------------------------------
           BPM
    ------------------------------------*/ 
    // +++++++++ BPM properties table
    $name = 'propertiesBPM';
    $id = 'propertiesBPM_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Interlock_Disabled',
        'AGC_Disabled'
    );
    //    createTableAttribute($conn, $name, $id, $cols);
    // +++++++++ BPM devices table 
    $name = 'devicesBPM';
    $id = 'devicesBPM_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'BPM',
    );
    //   createTableAttribute($conn, $name, $id, $cols);
    // +++++++++ BPM table 
    $name = 'BPM';
    $id = 'BPM_id';
    $foreignTables =  array('propertiesBPM','devicesBPM');
    //  createTableClass($conn, $name,$id, $foreignTables);

    /*-----------------------------------
            Bunch by Bunch
    ------------------------------------*/ 
    // +++++++++ BBB properties table
    $name = 'propertiesBBB';
    $id = 'propertiesBBB_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Avg_Horizontal_Tune',
        'Avg_Vertical_Tune',
        'FB_Pattern',
        'Input_File',
        'SRAM_peak_Freq_1',
        'SRAM_peak_Freq_2',
        'State'
    );
    //    createTableAttribute($conn, $name, $id, $cols);
    // +++++++++ BBB devices table 
    $name = 'devicesBBB';
    $id = 'devicesBBB_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'TUMON',
        'BBB_X',
        'BBB_Y',
        'BBB_Z'
    );
    //   createTableAttribute($conn, $name, $id, $cols);
    // +++++++++ BBB table 
    $name = 'BBB';
    $id = 'BBB_id';
    $foreignTables =  array('propertiesBBB','devicesBBB');
    //  createTableClass($conn, $name,$id, $foreignTables);

    /*-----------------------------------
            Beam Monitor
    ------------------------------------*/ 
    // +++++++++ BEMON properties table
    $name = 'propertiesBEMON';
    $id = 'propertiesBEMON_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Avg_Energy_Spread',
        'Avg_Horizontal_Emittance',
        'Avg_Vertical_Emittance',
        'Hor_Beam_Size',
        'Ver_Beam_Size'
    );
    // +++++++++ BEMON devices table 
    $name = 'devicesBEMON';
    $id = 'devicesBEMON_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'BEMON',
        'OptLine_1',
        'OptLine_2'
    );
    // +++++++++ BEMON table 
    $name = 'BEMON';
    $id = 'BEMON_id';
    $foreignTables =  array('propertiesBEMON','devicesBEMON');
    
    /*-----------------------------------
            DCCT
    ------------------------------------*/ 
    // +++++++++ DCCT properties table
    $name = 'propertiesDCCT';
    $id = 'propertiesDCCT_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Current',
        'Lifetime'
    );
    // +++++++++ DCCT devices table 
    $name = 'devicesDCCT';
    $id = 'devicesDCCT_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'DCCT'
    );
    // +++++++++ DCCT table 
    $name = 'DCCT';
    $id = 'DCCT_id';
    $foreignTables =  array('propertiesDCCT','devicesDCCT');


    /*-----------------------------------
            Bumps
    ------------------------------------*/ 
    // +++++++++ Bumps properties table
    $name = 'propertiesBUMP';
    $id = 'propertiesBUMP_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Hor_Angle',
        'Hor_Position',
        'Ver_Angle',
        'Ver_Position'
    );
    // +++++++++ Bumps devices table 
    $name = 'devicesBUMP';
    $id = 'devicesBUMP_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'MIK',
        'NamoMAX',
        'BALDER',
        'BioMAX',
        'CoSAXS',
        'Veritas',
        'Hippie',
        'SoftiMAX',
        'FlexPES',
        'Species',
        'Bloch',
        'MaxPEEM',
        'FinEst'
    );
    // +++++++++ Bumps table 
    $name = 'BUMP';
    $id = 'BUMP_id';
    $foreignTables =  array('propertiesBUMP','devicesBUMP');

  
    /*-----------------------------------
            CHOPPER
    ------------------------------------*/ 
    // +++++++++ CHOPPER properties table
    $name = 'propertiesCHOPPER';
    $id = 'propertiesCHOPPER_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Amplifier_Enable',
        'I_100_MHz',
        'I_300_MHz',
        'I_500_MHz',
        'I_700_MHz',
        'Q_100_MHz',
        'Q_300_MHz',
        'Q_500_MHz',
        'Q_700_MHz',
        'RF_IN_Select'
    );
    // +++++++++ CHOPPER devices table 
    $name = 'devicesCHOPPER';
    $id = 'devicesCHOPPER_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'CHOPPER'
    );
    // +++++++++ Bumps table 
    $name = 'CHOPPER';
    $id = 'CHOPPER_id';
    $foreignTables =  array('propertiesCHOPPER','devicesCHOPPER');

    /*-----------------------------------
            TIMING
    ------------------------------------*/ 
    // +++++++++ TIMING properties table
    $name = 'propertiesTIMING';
    $id = 'propertiesTIMING_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Out11_Delay',
        'Output00Delay',
        'Output01Delay',
        'Output02Delay',
        'Output03Delay',
        'Output04Delay',
        'Output05Delay',
        'Output06Delay',
        'Output07Delay',
        'Output08Delay',
        'Output08DelayFine',
        'Output09Delay',
        'Output09DelayFine',
        'Output10Delay',
        'Output10DelayFine',
        'Output11DelayFine'
    );
    // +++++++++ TIMING devices table 
    $name = 'devicesTIMING';
    $id = 'devicesTIMING_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'TIMING_1',
        'TIMING_2'
    );
    // +++++++++ TIMING table 
    $name = 'TIMING';
    $id = 'TIMING_id';
    $foreignTables =  array('propertiesTIMING','devicesTIMING');

    /*-----------------------------------
            FB
    ------------------------------------*/ 
    // +++++++++ FB properties table
    $name = 'propertiesFB';
    $id = 'propertiesFB_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Status',
        'Gain',
        'Max_Singular_Value',
        'RF_Correction',
        'Req_Correction_Frequency',
        'Sensor_Names',
        'Excluded_HCM',
        'Excluded_VCM'
    );
    // +++++++++ FB devices table 
    $name = 'devicesFB';
    $id = 'devicesFB_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'SOFB-01',
        'SOFB-02',
        'FB-03'
    );
    // +++++++++ FB table 
    $name = 'FB';
    $id = 'FB_id';
    $foreignTables =  array('propertiesFB','devicesFB');
 
    /*-----------------------------------
            KICKER
    ------------------------------------*/ 
    // +++++++++ KICKER properties table
    $name = 'propertiesKICKER';
    $id = 'propertiesKICKER_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Set_Current',
        'Set_Voltage'
    );
    // +++++++++ KICKER devices table 
    $name = 'devicesKICKER';
    $id = 'devicesKICKER_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'KICKER'
    );
    // +++++++++ KICKER table 
    $name = 'KICKER';
    $id = 'KICKER_id';
    $foreignTables =  array('propertiesKICKER','devicesKICKER');
 
    
    /*-----------------------------------
            CAVITIES
    ------------------------------------*/ 
    // +++++++++ CAVITIES properties table
    $name = 'propertiesCAVITIES';
    $id = 'propertiesCAVITIES_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Amplitude',
        'Margin_Low',
        'Margin_Up',
        'Minimum_Amplitude',
        'Potentiometer',
        'Temperature',
        'Phase',
        'TX_Forward_power',
        'Tuning_Offset'
    );
    // +++++++++ CAVITIES devices table 
    $name = 'devicesCAVITIES';
    $id = 'devicesCAVITIES_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'LANDAU_CAVITY_13',
        'LANDAU_CAVITY_14',
        'LANDAU_CAVITY_15',
        'MAIN_CAVITY_16',
        'MAIN_CAVITY_17',
        'MAIN_CAVITY_18',
        'MAIN_CAVITY_19',
        'MAIN_CAVITY_20',
        'MAIN_CAVITY_01',
        'LANDAU_CAVITY_A',
        'LANDAU_CAVITY_B',
        'MAIN_CAVITY_A',
        'MAIN_CAVITY_B'
    );
    // +++++++++ CAVITIES table 
    $name = 'CAVITIES';
    $id = 'CAVITIES_id';
    $foreignTables =  array('propertiesCAVITIES','devicesCAVITIES');

    

    /*-----------------------------------
            SCRAPERS
    ------------------------------------*/ 
    // +++++++++ SCRAPERS properties table
    $name = 'propertiesSCRAPERS';
    $id = 'propertiesSCRAPERS_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Position'
    );
    // +++++++++ SCRAPERS devices table 
    $name = 'devicesSCRAPERS';
    $id = 'devicesSCRAPERS_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'H1',
        'V1',
        'V2'
    );
    // +++++++++ SCRAPERS table 
    $name = 'SCRAPERS';
    $id = 'SCRAPERS_id';
    $foreignTables =  array('propertiesSCRAPERS','devicesSCRAPERS');


    /*-----------------------------------
            STM
    ------------------------------------*/ 
    // +++++++++ STM properties table
    $name = 'propertiesSTM';
    $id = 'propertiesSTM_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Feedback',
        'Injections_per_Step',
        'Nr_of_Buckets',
        'Snapshot',
        'Step_Length'
    );
    // +++++++++ STM devices table 
    $name = 'devicesSTM';
    $id = 'devicesSTM_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'STM'
    );
    // +++++++++ STM table 
    $name = 'STM';
    $id = 'STM_id';
    $foreignTables =  array('propertiesSTM','devicesSTM');


    /*-----------------------------------
            RF
    ------------------------------------*/ 
    // +++++++++ RF properties table
    $name = 'propertiesRF';
    $id = 'propertiesRF_id';
    $cols =  array('property VARCHAR(255)');
    $properties = array(
        'Frequency'
    );
    // +++++++++ RF devices table 
    $name = 'devicesRF';
    $id = 'devicesRF_id';
    $cols =  array('device VARCHAR(255)');
    $devices = array(
        'RF'
    );
    // +++++++++ RF table 
    $name = 'RF';
    $id = 'RF_id';
    $foreignTables =  array('propertiesRF','devicesRF');

?>