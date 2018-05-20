<?php

class Maga_Video_Block_Chart extends Mage_Core_Block_Template
{
    public function getJsonConfig()
    {
        $data = array(
            'type' => 'bar',
            'options' => $this->_getOptions(),
            'data' => array(
                'labels' => $this->_getLabels(),
                'datasets' => $this->_getDatasets(),
            )
        );
        return json_encode($data);
    }

    protected function _getOptions()
    {
        $options = array(
            'scales' => array(
                'yAxes' => array(
                    array('ticks' => array('beginAtZero' => true))
                )
            ),
        );
        return $options;
    }

    protected function _getLabels()
    {
        $labels = array(
            'Red',
            'Blue',
            'Yellow',
            'Orange'
        );

        return $labels;
    }

    protected function _getDatasets ()
    {
        $data = array(
            array(
                'label' => '# of Votes',
                'data' => array(
                    12,
                    10,
                    2,
                    3),
                'backgroundColor' => array(
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                ),
                'borderColor' => array(
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                ),
                'borderWidth' => 10
            )
        );

        return $data;
    }
}