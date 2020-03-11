<?php

namespace Flex360\Pilot\Pilot;

class EventPresenter extends Presenter
{

    public function getDateString()
    {
        $startFormat = 'n/j/Y g:i a';

        $endFormat = 'n/j/Y g:i a';

        $separator = ' - ';

        // if start and end dates are the same, only show time of end date
        if ($this->entity->start->toDateString() == $this->entity->end->toDateString()) {
            $endFormat = 'g:i a';
        }

        // if times are both, 12 am assume no time wanted
        if ($this->entity->start->toTimeString() == '00:00:00' && $this->entity->end->toTimeString() == '00:00:00') {
            $startFormat = 'n/j/Y';

            $endFormat = '';

            $separator = null;
        }

        return $this->entity->start->format($startFormat) . $separator . $this->entity->end->format($endFormat);
    }
}
