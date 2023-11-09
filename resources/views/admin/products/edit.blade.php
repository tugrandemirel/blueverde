@extends('admin.layouts.app')
@section('title', 'Ürün Düzenle')
@section('css')
    <link href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
@endsection
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Ekle</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Ürün Düzenle</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.product.update', ['product' => $product]) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="border p-4 rounded">
                            <div class="card-title d-flex align-items-center">
                                <h5 class="mb-0">Ürün Düzenle</h5>
                            </div>
                            <hr>

                            @if($productTags->count() > 0)
                            <div class="row  mt-3">
                                <label class="col-sm-4 col-form-label">Ürün Etiket</label>
                                <div class="col-sm-6">
                                <select class="form-select @error('product_tag_id') is-invalid @enderror" name="product_tag_id">
                                    <option selected>Etiket Seçiniz</option>
                                    @foreach($productTags as $tag)
                                        <option value="{{ $tag->id }}" @if($product->product_tag_id == $tag->id) selected @endif>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            @else
                                <div class="row  mt-3">
                                    <label class="col-sm-4 col-form-label">Ürün Etiketi</label>
                                    <div class="col-sm-6">
                                        <div class="alert alert-warning">
                                            Lütfen <strong>Ürün Etiketi</strong> Ekleyiniz.
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($categoryTags->count() > 0)
                                <div class="row  mt-3">
                                    <label class="col-sm-4 col-form-label">Kategori Etiketi</label>
                                    <div class="col-sm-6">
                                        <select name="" id="categoryTag" class="form-control">
                                            <option value="">Kategori Etiketi Seçiniz</option>
                                            @foreach($categoryTags as $tag)
                                                <option value="{{ $tag->id }}" @if($tag->id == $product->category->categoryTag->id) selected @endif>{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            @else
                                <div class="row  mt-3">
                                    <label class="col-sm-4 col-form-label">Kategori Etiketi</label>
                                    <div class="col-sm-6">
                                        <div class="alert alert-warning">
                                            Lütfen <strong>Kategori Etiketi</strong> Ekleyiniz.
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($categories->count() > 0)
                            <div class="row  mt-3">
                                <label class="col-sm-4 col-form-label">Kategori</label>
                                <div class="col-sm-6">
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category-results" name="category_id">
                                        <option selected>Kategori Seçiniz</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"  @if($product->category_id == $category->id) selected @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @else
                                <div class="row  mt-3">
                                    <label class="col-sm-4 col-form-label">Kategori</label>
                                    <div class="col-sm-6">
                                        <div class="alert alert-warning">
                                            Lütfen <strong>Kategori</strong> Ekleyiniz.
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row  mt-3">
                                <label class="col-sm-4 col-form-label">Ürün Adı</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $product->name }}" name="name" placeholder="Ürün Adı Giriniz">
                                </div>
                            </div>
                            <div class="row  mt-3">
                                <label class="col-sm-4 col-form-label">Ürün Kodu</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $product->code }}" placeholder="Ürün Kodu Giriniz">
                                </div>
                            </div>
                            <div class="row  mt-3">
                                <label class="col-sm-4 col-form-label">Ürün Fiyatı</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ $product->price }}" placeholder="Ürün Fiyatı Giriniz">
                                </div>
                            </div>
                            <div class="row  mt-3">
                                <div class="col-sm-12">
                                    <label for="">Ürün Açıklaması</label>
                                    <textarea class="form-control ckeditor1 @error('description') is-invalid @enderror" name="description" rows="4" placeholder="Ürün Açıklaması Giriniz">{{ $product->description }}</textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary px-5">Güncelle</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')

    <script>

        $(document).ready(function() {
            $('#categoryTag').change(function () {
                let id = $(this).val();
                $.ajax({
                    url: '{{ route('admin.category.getCategoryId') }}', // AJAX isteğini yönlendireceğiniz route'unuzu belirtin
                    method: 'GET',
                    data: { tag_id: id },
                    success: function (response) {
                        $('#category-results').empty();
                        $.each(response, function (key, value) {
                            $('#category-results').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            })
        });

    </script>
    <script>
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        let ck = document.querySelectorAll('.ckeditor1');
        for (let i = 0; i < ck.length; i++) {
            CKEDITOR.replace(ck[i], {
                height: 650,
                filebrowserUploadUrl: "{{route('admin.upload', ['_token' => csrf_token() ])}}",
                filebrowserUploadMethod: 'form'
            });
        }
    </script>
@endsection
