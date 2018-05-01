<?php

class TBT_Rewards_Helper_Metrics_NumberOfMembers_Total extends TBT_Rewards_Helper_Metrics_NumberOfMembers
{

    /**
     * Overwrites parent method and calculates the total count of new loyalty members for the selected period.
     *
     * @param  array  $series [description]
     * @return TBT_Rewards_Helper_Metrics_NumberOfMembers_Total
     */
    protected function _prepareSeries($series = array())
    {
        if (is_null($series)) {
            return $this;
        }

        $totalMembers = 0;
        foreach ($series as $key => $data) {
            if (isset($data['members'])) {
                $totalMembers += $data['members'];
            }
        }
        $this->addSeries(array(
            'title'   => 'Total Number of New Members',
            'content' => $totalMembers,
            'note'    => 'in the selected period.'
        ));

        return $this;
    }
}
