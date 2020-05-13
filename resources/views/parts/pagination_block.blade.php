<div class="pagination_block">
    <div class="row">
        <div class="product-quantity-list"> Показано <strong>{{ ($orders->currentPage() - 1) * $orders->perPage() + 1}} - {{$orders->count() + (($orders->currentPage() - 1) * $orders->perPage())}}</strong> из {{ $orders->total() }}</div>
        <div class="product-page-num">
            <form>
                <select class="product-page-num-sel">
                    @php $perPages = [20 => 20, 50 => 50, 100 => 100, 'Все' => $orders->total()]; @endphp
                    @foreach($perPages as $one=>$value)
                        @if($value == $perPage)
                            <option value="{{ $value}}" selected>{{ $one }}</option>
                        @else
                            <option value="{{ $value }}">{{ $one }}</option>
                        @endif
                    @endforeach
                </select>
            </form>
        </div>
	</div>
	<div class="row">
        <div class="product-page-list">
            {{ $orders->links() }}
        </div>
    </div>
</div>