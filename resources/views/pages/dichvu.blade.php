@extends('layouts.index')

{{-- Phần Heading --}}
@section('heading')
   Thông tin dịch vụ
@endsection

@section('breadcrumb')
<a href="{{ route('DichVu.view') }}">
    Dịch vụ
</a> >
@endsection

{{-- Phần content --}}
@section('content')

<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-body">
                {{-- {{route('DichVu.update', $dichvu->id)}} --}}
                <form action="{{route('DichVu.update', $dichvu->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                
                    <div class="form-group">
                        <label for="dien">Tiền điện</label>
                        <input type="text" name="dien" id="dien" class="form-control" 
                               value="{{ number_format($dichvu->dien, 0, ',', '.') }}" 
                               placeholder="" required>  
                        @error('dien')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror      
                    </div>
                    
                    <div class="form-group">
                        <label for="nuoc">Tiền nước</label>
                        <input type="text" name="nuoc" id="nuoc" class="form-control" 
                               value="{{ number_format($dichvu->nuoc, 0, ',', '.') }}" 
                               placeholder="" required>   
                        @error('nuoc')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror       
                    </div>
                    
                    <div class="form-group">
                        <label for="wifi">Tiền Wifi</label>
                        <input type="text" name="wifi" id="wifi" class="form-control" 
                               value="{{ number_format($dichvu->wifi, 0, ',', '.') }}" 
                               placeholder="" required>  
                        @error('wifi')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror        
                    </div>
                    
                    <div class="form-group">
                        <label for="guixe">Tiền gửi xe</label>
                        <input type="text" name="guixe" id="guixe" class="form-control" 
                               value="{{ number_format($dichvu->guixe, 0, ',', '.') }}" 
                               placeholder="" required>       
                        @error('guixe')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror   
                    </div>
                    
                    <div class="form-group">
                        <label for="rac">Tiền rác</label>
                        <input type="text" name="rac" id="rac" class="form-control" 
                               value="{{ number_format($dichvu->rac, 0, ',', '.') }}" 
                               placeholder="" required>    
                        @error('rac')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror      
                    </div>
                    



                    {{-- Nút Submit --}}
                    @if(!session()->has('user_type'))
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                    @endif
                </form>
                

            </div> <!-- End Card Body -->
        </div> <!-- End Column -->
    </div> <!-- End Row -->
</div> <!-- End Container -->


{{-- JavaScript để quản lý trạng thái --}}

@endsection


