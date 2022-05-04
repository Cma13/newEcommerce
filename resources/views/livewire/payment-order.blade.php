<div>
    <div class="grid grid-cols-5 gap-6 container-menu py-8">
        <div class="col-span-3">
            <div class="bg-white rounded-lg shadow-lg px-6 py-4 mb-6">
                <p class="text-gray-700"><span class="font-semibold">Número de pedido: </span>Pedido -
                    {{ $order->id }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-6 text-gray-700">
                    <div>
                        <p class="text-lg font-semibold">Envío</p>
                        @if ($order->envio_type == 1)
                            <p class="text-sm">Los productos deben ser recogidos en tienda</p>
                            <p class="text-sm">Calle falsa 123</p>
                        @else
                            <p class="text-sm">Los productos serán enviados a:</p>
                            <p class="text-sm">{{ $order->address }}</p>
                            <p>{{ $order->department->name }} - {{ $order->city->name }} -
                                {{ $order->district->name }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-lg font-semibold">Datos de contacto:</p>
                        <p class="text-sm">Persona que recibirá el producto: {{ $order->contact }}</p>
                        <p class="text-sm">Teléfono de contacto: {{ $order->phone }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6">
                <p class="text-xl font-semibold mb-4">Resumen</p>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Precio</th>
                            <th>Cant</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($items as $item)
                            <tr>
                                <td>
                                    <div class="flex">
                                        <img class="h-15 w-20 object-cover mr-4" src="{{ $item->options->image }}"
                                            alt="">
                                        <article>
                                            <h1 class="font-bold">{{ $item->name }}</h1>
                                            <div class="flex text-xs">
                                                @isset($item->options->color)
                                                    Color: {{ __(ucfirst($item->options->color)) }}
                                                @endisset

                                                @isset($item->options->size)
                                                    - {{ $item->options->size }}
                                                @endisset
                                            </div>
                                        </article>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->price }}&euro;</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-center">{{ $item->price * $item->qty }}&euro;</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow-lg px-6 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <img class="h-10" src="{{ asset('img/payment-methods.png') }}" alt="payment-methods">
                    <div class="text-gray-700">
                        <p class="text-sm font-semibold">Subtotal: {{ $order->total - $order->shipping_cost }}&euro;
                        </p>
                        <p class="text-sm font-semibold">Envío: {{ $order->shipping_cost }}&euro;</p>
                        <p class="text-lg font-semibold">Total: {{ $order->total }}&euro;</p>
                    </div>
                </div>
                <div id="paypal-button-container"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR">
        </script>
        <script>
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: "{{ $order->total }}"
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(orderData) {
                        // console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                        // var transaction = orderData.purchase_units[0].payments.captures[0];
                        // alert('Transaction ' + transaction.status + ': ' + transaction.id +
                        //     '\n\nSee console for all available details');
                        Livewire.emit('payOrder');
                    });
                }
            }).render('#paypal-button-container');
        </script>
    @endpush
</div>