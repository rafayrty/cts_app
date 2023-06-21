<?php

namespace App;

enum PrintHouseStatusEnum: string
{
    case NEW_ORDER = 'new order';
    case WAITING_FOR_APPROVAL = 'waiting for approval';
    case APPROVED = 'approved';
    case WORKING_ON_IT = 'working on it';
    case STARTING = 'starting printing';
    case PACKAGING = 'packaging';
    case READY_FOR_DELIVERY = 'ready for delivery';
    case DONE = 'done';
    case FINISHING = 'finishing';
    case STUCK = 'stuck';
    case CANCEL = 'cancel';
}
