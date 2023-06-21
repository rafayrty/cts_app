{{--<script src="{{asset('html5-qrcode.min.js')}}" ></script>--}}
  <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
  <script type="text/javascript">
    window.addEventListener('load', function () {
    })
  </script>
    <div class="printing-page filament-stats-card relative p-4 rounded-md bg-white shadow dark:bg-gray-800 filament-stats-overview-widget-card "
        x-data="{
            barcode:'',
            book_loading:false,
            book_added:null,
            cover_found:null,
            init(){
                document.querySelector('.filament-sidebar-item .bg-primary-500').classList.remove('bg-primary-500');
               let selectedDeviceId;
      const codeReader = new ZXing.BrowserBarcodeReader()
      console.log('ZXing code reader initialized')
      codeReader.listVideoInputDevices()
        .then((videoInputDevices) => {
          const sourceSelect = document.getElementById('sourceSelect')
          selectedDeviceId = videoInputDevices[0].deviceId
          if (videoInputDevices.length >= 1) {
            videoInputDevices.forEach((element) => {
              const sourceOption = document.createElement('option')
              sourceOption.text = element.label
              sourceOption.value = element.deviceId
              sourceSelect.appendChild(sourceOption)
            })

            sourceSelect.onchange = () => {
              selectedDeviceId = sourceSelect.value;
            };

            const sourceSelectPanel = document.getElementById('sourceSelectPanel')
            sourceSelectPanel.style.display = 'block'
          }

          document.getElementById('startButton').addEventListener('click', () => {
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
              if (result) {
                this.findData(result)
                document.getElementById('result').textContent = result.text
              }
              if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err)
                document.getElementById('result').textContent = err
              }
            })
            console.log(`Started continous decode from camera with id ${selectedDeviceId}`)
          })

          document.getElementById('resetButton').addEventListener('click', () => {
            codeReader.reset()
            document.getElementById('result').textContent = '';
            console.log('Reset.')
          })

        })
        .catch((err) => {
          console.error(err)
        })
},
            onScanSuccess(decodedText, decodedResult) {
                this.barcode = decodedText;
                console.log(`Scan result: ${decodedText}`, decodedResult);
            },
            validateString(str) {
                const pattern = /^\d+-\d+-\d+-\d+$/;
                if (pattern.test(str)) {
                    return true;
                } else {
                    return false;
                }
            },
            resetForm(){
                this.book_added = null;
                this.cover_found = null;
                this.barcode = '';
            },
            findBook(){
                this.book_loading = true;
                fetch('/find_book/'+this.barcode, {
                  method: 'GET',
                  headers: {
                    'Accept': 'application/json'
                  }
                })
                    .then(response => {
                        if (!response.ok) {
                          // Read the error response as JSON
                          return response.json().then(errorData => {
                            throw new Error(errorData.message);
                          });
                        }
                        return response.json();
                      })
                      .then(data => {
                        // Access the JSON data
                        this.book_added = data;

                        alert('A Book was Found')
                        this.barcode = '';
                        this.book_loading = false;
                      })
                      .catch(error => {
                        console.error('Error:', error);
                        alert(error);
                        this.book_loading = false;

                        this.barcode= '';
                });
            },
            findCover(){
                this.book_loading = true;
                if(this.book_added!=null){
                fetch('/find_cover/'+this.barcode+'/'+this.book_added.id, {
                  method: 'GET',
                  headers: {
                    'Accept': 'application/json'
                  }
                })
                    .then(response => {
                        if (!response.ok) {
                          // Read the error response as JSON
                          return response.json().then(errorData => {
                            throw new Error(errorData.message);
                          });
                        }
                        return response.json();
                      })
                      .then(data => {
                        // Access the JSON data
                        this.cover_found = data;
                        this.barcode = '';
                        this.book_loading = false;
                        alert('Corresponding Cover is Found')
                        if(confirm('Do You want to Reset and Proceed With Another Book')){
                          this.resetForm()
                        }
                      })
                      .catch(error => {
                        console.error('Error:', error);
                        alert(error);
                        this.cover_found = null;
                        this.book_loading = false;
                        this.barcode = ''
                });

                }else{
                    alert('An Unexpected error please reset and start again');
                }

            },
            findData(decodedText){
                if(!this.validateString(decodedText)){
                    alert('Barcode was not scanned Successfully try again')
                }
                this.barcode = decodedText;
                if(this.book_added == null){
                    this.findBook()
                }else{
                    this.findCover()
                }
            }
        }" >
        <form x-on:submit.prevent="findData">
        <div>
        <a class="button" style="background:#8da12b;" id="startButton">Start</a>
        <a class="button" id="resetButton">Reset</a>
      </div>
      <div>
        <video id="video" width="300" height="250" style="border: 1px solid gray"></video>
      </div>

      <div id="sourceSelectPanel" style="display:none">
        <label for="sourceSelect">Change video source:</label>
        <select id="sourceSelect"  style="max-width:400px;color:black;">
        </select>
      </div>

      <label>Result:</label>
        <template x-if="book_added == null">
            <h1 class="mt-2 mb-2 font-semibold" >Enter or Scan a Book's Barcode To Proceed</h1>
        </template>
        <template x-if="book_added != null">
            <h1 class="mt-2 mb-2 font-semibold" x-if="book_added != null">Find The Corresponding Cover</h1>
        </template>
        <input type="text" x-model="barcode" name="barcode" placeholder="1024-13-22-123" class="mt-2 block w-full h-10 bg-gray-400/10 placeholder-gray-500 border-transparent transition duration-75 rounded-lg outline-none focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400"/>
<div class="flex items-center justify-end mt-4" >

<template x-if="book_added != null">
    <button type="reset"  x-on:click="resetForm()" class="filament-button mr-2 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-page-button-action">
    <span>Reset</span>
    </button>
</template>

<button type="submit" x-bind:disabled="book_loading"  class="filament-button self-end filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action" href="https://basmti.test/admin/orders/create" dusk="filament.admin.action.create">
<template x-if="book_loading">
<svg viewBox="0 0 24 24" >
    <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"></path>
    <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor"></path>
</svg>
</template>
        <span class="">
            Submit
        </span>
    </button>
    </div>

<div class="book-information">

</div>
<template x-if="book_added != null">
<div class="flex items-center">
    <img x-bind:src="`https://basmti.test/${book_added.image}`" width="100" height="100" class="rounded-md"/>
    <div class="book-info ml-4">
    <ul>
        <li class="font-semibold">Book Name: <span class="font-normal" x-text="book_added.name"></span></li>
        <li class="font-semibold">Order Id: <span class="font-normal" x-text="book_added.order_id"></span></li>
    </ul>
    </div>
</div>
</template>


<template x-if="cover_found != null">
<div class="flex items-center">
    <h1>Corresponding Cover Found </h1>
    <div class="book-info ml-4">
    <ul>
        <li class="font-semibold">Cover Type: <span class="font-normal" x-text="cover_found.type == 1 ? 'Hard Cover' : 'Soft Cover' "></span></li>
        <li class="font-semibold">Order Id: <span class="font-normal" x-text="book_added.order_id"></span></li>
    </ul>
    </div>
</div>
</template>

    </div>
  <script type="text/javascript">
</script>
<style>
#reader{
width:100%;
}

#reader__scan_region img{
    margin:0 auto;
}
button.html5-qrcode-element{
    background: #0DAA91;
    padding: 0.5rem;
    border-radius: 0.5rem;
    margin-bottom: 0.4rem;
}
@media only screen and (min-width:768px){
#reader{
    width:400px;
    margin:0 auto;
}
}
</style>
