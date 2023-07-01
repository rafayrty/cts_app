<?php

namespace App\Forms\Components;

use Closure;
use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Str;

class CustomRepeater extends Repeater
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultItems(1);

        $this->afterStateHydrated(static function (Repeater $component, ?array $state): void {
            $items = [];

            foreach ($state ?? [] as $itemData) {
                $items[(string) Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->registerListeners([
            'repeater::createItem' => [
                function (Repeater $component, string $statePath): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set($livewire, "{$statePath}.{$newUuid}", []);

                    $component->getChildComponentContainers()[$newUuid]->fill();

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'repeater::deleteItem' => [
                function (Repeater $component, string $statePath, string $uuidToDelete): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = $component->getState();
                    $livewire = $component->getLivewire();

                    unset($items[$uuidToDelete]);

                    $livewire = $component->getLivewire();

                    data_set($livewire, $statePath, $items);

                    $this->updated_documents($component);
                    //dd(data_get($livewire,'data.Documents'));
                },
            ],
            'repeater::cloneItem' => [
                function (Repeater $component, string $statePath, string $uuidToDuplicate): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set(
                        $livewire,
                        "{$statePath}.{$newUuid}",
                        data_get($livewire, "{$statePath}.{$uuidToDuplicate}"),
                    );

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'repeater::moveItemDown' => [
                function (Repeater $component, string $statePath, string $uuidToMoveDown): void {
                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_move_after($component->getState(), $uuidToMoveDown);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveItemUp' => [
                function (Repeater $component, string $statePath, string $uuidToMoveUp): void {
                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_move_before($component->getState(), $uuidToMoveUp);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveItems' => [
                function (Repeater $component, string $statePath, array $uuids): void {
                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_merge(array_flip($uuids), $component->getState());

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
        ]);

        $this->createItemButtonLabel(static function (Repeater $component) {
            return __('forms::components.repeater.buttons.create_item.label', [
                'label' => lcfirst($component->getLabel()),
            ]);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }


    public function updated_documents(Repeater $component){

        $livewire = $component->getLivewire();

        $documents = data_get($livewire,'data.Documents');
        $file_names = [];
        foreach ($documents as $document) {
            $file_names[] = $document['pdf_name'];
        }
        $pdf_info = json_decode(data_get($livewire,'data.pdf_info'), true);

        if ($pdf_info) {
            $found_key = null;
            $new_array = [];
            $pages = data_get($livewire,'data.pages');
            $new_pages = [];
            $dedications = data_get($livewire,'data.dedications');
            $new_dedications = [];
            $barcodes = data_get($livewire,'data.barcodes');
            $new_barcodes = [];
            foreach ($pdf_info as $pdf_in) {
                foreach ($documents as $key => $document) {
                    if (array_key_exists('pdf_name', $document)) {
                        if ($pdf_in['filename'] == $document['pdf_name']) {
                            $new_array[] = $pdf_in;
                        }
                    }
                }
            }
            if (count($documents) != count($pdf_info)) {
                foreach($new_array as $document){
                    if($pages){

                        foreach ($pages as $key => $page) {
                            if ($page['document'] == $document['filename']) {
                                $new_pages[$key] = $page;
                            }
                        }
                        data_set($livewire,'data.pages', $new_pages);

                    }
                    if($dedications){

                        foreach ($dedications as $key => $dedication) {
                            if ($dedication['document'] == $document['filename']) {
                                $new_dedications[$key] = $dedication;
                            }
                        }

                        data_set($livewire,'data.dedications', $new_dedications);

                    }
                    if($barcodes){

                        foreach ($barcodes as $key => $barcode) {
                            if ($barcode['document'] == $document['filename']) {
                                $new_barcodes[$key] = $barcode;
                            }
                        }
                        data_set($livewire,'data.barcodes', $new_barcodes);

                    }
                }
                if(count($new_array)==0){
                    data_set($livewire,'data.pages', []);
                    data_set($livewire,'data.dedications', []);
                    data_set($livewire,'data.barcodes', []);

                }
            }
            data_set($livewire,'data.pdf_info', (count($new_array) > 0) ? json_encode($new_array) : "");
        }
    }

}
