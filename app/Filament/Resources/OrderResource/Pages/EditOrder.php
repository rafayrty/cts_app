<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Mail\OrderCancel;
use App\Mail\OrderUpdate;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            //Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->client_status->value != $data['client_status']) {
            if ($data['client_status'] == 'packaging') {
                $title = 'طلبك قيد التغليف';
                $text = 'يسعدنا إخبارك بأنه تمت معالجة طلبك ونحن الآن في طور التعبئة والتغليف. سيتم إرسال طلبك في أقرب وقت ممكن.';
                Mail::to($this->record->user->email)->queue(new OrderUpdate($this->record, $title, $text));
            }
            if ($data['client_status'] == 'ready for delivery') {
                $title = 'طلبك جاهز للشحن';
                $text = 'سعداء جداً لإعلامك بأن طلبك قد تم إنهائه وهو جاهز الآن للتوصيل. سيتم إرسال بريد إلكتروني يحتوي على معلومات الشحن الخاصة بطلبك';
                Mail::to($this->record->user->email)->queue(new OrderUpdate($this->record, $title, $text));
            }

            if ($data['client_status'] == 'cancel') {
                Mail::to($this->record->user->email)->queue(new OrderCancel($this->record));
            }
        }

        return $data;
    }
}
