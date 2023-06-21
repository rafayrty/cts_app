<script src="{{asset('html5-qrcode.min.js')}}" ></script>
    <div class="packaging-page filament-stats-card relative p-4 rounded-md bg-white shadow dark:bg-gray-800 filament-stats-overview-widget-card "
        x-data="{
            barcode:'',
            orders:[], // {order_id:null,order:{},items:[],all_found:0}
            loading:false,
            timer:0,
            init(){
                document.querySelector('.filament-sidebar-item .bg-primary-500').classList.remove('bg-primary-500');
                var html5QrcodeScanner = new Html5QrcodeScanner(
                    'reader', { fps: 10, qrbox: {width:240,height:140} });
                let self = this;
                html5QrcodeScanner.render((decodedText)=>{
                    this.findPackagingOrder(decodedText);
                    html5QrcodeScanner.pause()
                    window.setTimeout(()=>{
                        html5QrcodeScanner.resume()
                    },1500)
                });
            },
            resetForm(){
                this.orders = [];
                this.barcode = '';
            },
            validateString(str) {
                const pattern = /^\d+-\d+-\d+-\d+$/;
                if (pattern.test(str)) {
                    return true;
                } else {
                    return false;
                }
            },
            findPackagingOrder(decodedText){
                    if(!this.validateString(decodedText)){
                       alert('Barcode was not scanned Successfully try again')
                       return;
                    }

                this.loading = true;
                    this.barcode = decodedText;
                    this.findPackagingOrder();
                fetch('/find_packaging_order/'+this.barcode, {
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
                        if(this.orders.length == 0){
                            this.orders.push({
                                order_id:data.order.id,
                                order:data.order,
                                items:[data.order_item],
                                all_found:data.order.items.length,
                            })
                        }else{
                            var order_index = this.orders.findIndex(function(obj){
                                return obj.order_id === data.order.id
                            })
                            //If order is not new
                            if(order_index !== -1){
                                    //Check if item is new
                                    var item_index = this.orders[order_index].items.findIndex(function(obj) {
                                              console.error(obj.id,data.order_item.id,'heyy')
                                              return obj.id === data.order_item.id;
                                    });
                                    //If not found in it then add
                                    if(item_index == -1){
                                        this.orders[order_index].items.push(data.order_item)
                                        if(this.orders[order_index].all_found == this.orders[order_index].items.length){
                                            alert('All Items found for Order ID# '+this.orders[order_index].order_id)
                                        }
                                    }else{
                                        alert('Item Already Exists');
                                    }
                            }else{
                                alert('This Book Belongs to a new Order With ID#'+data.order.order_numeric_id)
                                this.orders.push({
                                    order_id:data.order.id,
                                    order:data.order,
                                    items:[data.order_item],
                                    all_found:data.order.items.length,
                                })
                            }
                            this.orders.forEach((order)=>{
                                if(order.id === data.order.id){
                                    // If Order Already Exists Check if the Item is New or Not
                                    order.items.forEach((item)=>{
                                        //Checking item is new or not
                                        if(item.id!=data.order_item.id){
                                            //If not new get the index of the order array
                                            var index = order.items.findIndex(function(obj) {
                                              return this.orders.order_id === order.order_id;
                                            });
                                            //Push the new item
                                            this.orders[index].items.push(item.id);
                                        }
                                    })
                                }
                             })
                        }
                        this.barcode = '';
                        this.loading = false;
                      })
                      .catch(error => {
                        console.error('Error:', error);
                        alert(error);
                        this.loading = false;
                        this.barcode= '';
                });
            },
        }" >
        <form x-on:submit.prevent="findPackagingOrder">
<div id="reader"></div>

        <h1 class="mt-2 mb-2 font-semibold" >Start Packaging By Scanning</h1>
        <input type="text" x-model="barcode" name="barcode" placeholder="1024-13-22-123" class="mt-2 block w-full h-10 bg-gray-400/10 placeholder-gray-500 border-transparent transition duration-75 rounded-lg outline-none focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400"/>
<div class="flex items-center justify-end mt-4" >

    <button type="reset"  x-on:click="resetForm()" class="filament-button mr-2 filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-page-button-action">
    <span>Reset</span>
    </button>

<button type="submit" x-bind:disabled="loading"  class="filament-button self-end filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action" href="https://basmti.test/admin/orders/create" dusk="filament.admin.action.create">
<template x-if="loading">
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
<template x-if="orders.length != 0">
    <template x-for="order in orders">
    <div>
    <h1 x-text="`Order ID# ${order.order.order_numeric_id}`"> </h1>
    <template x-if="order.all_found === order.items.length">

<div>
        <h1 class="font-semibold" style="color:#8da12b;"> All Items Found (Send for Packaging) </h1>

</div>
    </template>
    <h1 x-text="`${order.items.length} found out of ${order.all_found}`"> </h1>
    <a  class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"
      x-bind:href="`/admin/orders/${order.order_id}`">View Order Info</a>
        <template x-for="item in order.items">
        <div class="flex items-center mt-4">
            <img x-bind:src="`https://basmti.test/${item.image}`" width="100" height="100" class="rounded-md"/>
            <div class="book-info ml-4">
            <ul>
                <li class="font-semibold">Product Name: <span class="font-normal" x-text="item.name"></span></li>
            </ul>
            </div>
        </div>
        </template>
        <hr class="mt-2 mb-2"/>
    </div>
    </template>
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

<script>
document.querySelectorAll('.print-status-switcher').forEach(elem=>{
      elem.addEventListener('change', function(event) {
          const id = event.target.getAttribute('data-id')
          const print_status = event.target.value

          window.location.href = "{{route('order.update_print.status')}}/"+id+"/"+print_status
      });
  })
  @if(Session::has('success'))
   if(!window.shownMessage) {
       //alert("Print House Status Updated Successfully");
       window.shownMessage = true;
   }
@endif
</script>
