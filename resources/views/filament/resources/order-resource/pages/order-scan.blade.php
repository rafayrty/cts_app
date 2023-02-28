<x-filament::page>
    <div class="container bg-gray-100 dark:bg-gray-800 rounded-md py-8 px-6">
      <form action="" wire:submit.prevent="submit">

        <div class="flex flex-col text-center justify-center items-center">
          <h1 class="font-bold text-center text-3xl">Enter Barcode Number To Scan Order</h1>
          <img src="{{asset('images/barcode-admin.png')}}"/>
          <div class="w-full">
          <input class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"
            type="text" wire:model="barcode_number" placeholder="Enter Barcode Number To Proceed"/>

          @error('barcode_number') <p class="text-left filament-forms-field-wrapper-error-message text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p> @enderror
          </div>
          <div class="mt-4"></div>
          <input type="submit" wire:loading.class="disabled" class="mt-4 filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action"
          />
        </div>
        </form>
    </div>
</x-filament::page>
