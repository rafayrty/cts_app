<?php

namespace App;

enum PrintHouseStatusEnum: string
{
    case STARTING = 'starting printing';
    case PACKAGING = 'packaging';
    case READY_FOR_DELIVERY = 'ready for delivery';
    case DONE = 'done';
    case STUCK = 'stuck';
    case CANCEL = 'cancel';
}