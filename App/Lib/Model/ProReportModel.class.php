<?php
    class ProReportModel extends ViewModel {
        public $viewFields = array (
            'report' => array('id', 'dept_id', 'date'),
            'report_value' => array('cname', 'cvalue', '_on' => 'report.id = report_value.report_id'),
            'report_column' => array('formula','_on' => 'report_value.reportc_id = report_column.id')
        );
    }
?>