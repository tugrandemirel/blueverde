<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Customer\CustomerPersonalTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\OfferStoreRequest;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Product;
use App\Models\ProductOffer;
use App\Models\ProductTag;
use App\Models\SystemDeliveryMethod;
use App\Models\SystemTermOfOffer;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::with(['customer', 'delivery', 'productTag'])->get();
        return view('admin.offer.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productTags = ProductTag::all();
        $term_of_offers = SystemTermOfOffer::all();
        $deliveries = SystemDeliveryMethod::all();
        return view('admin.offer.create', compact('productTags', 'term_of_offers', 'deliveries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfferStoreRequest $request): RedirectResponse
    {
        $data = collect($request->validated());

        $items = ($data['products']);
        unset($data['products']);
        $create = Offer::create($data->toArray());

        $products = [];
        foreach ($items['name'] as $key => $name) {

            $products[$key]['product_id'] = $items['product_id'][$key];
            $products[$key]['offer_id'] = $create->id;
            $products[$key]['name'] = $items['name'][$key];
            $products[$key]['category'] = $items['category'][$key];
            $products[$key]['product_size'] = $items['product_size'][$key];
            $products[$key]['type'] = $items['type'][$key];
            $products[$key]['material'] = $items['material'][$key];
            $products[$key]['color'] = $items['color'][$key];
            $products[$key]['detail'] = $items['detail'][$key];
            $products[$key]['quantity'] = (int)$items['quantity'][$key];
            $products[$key]['price'] = (float)$items['price'][$key];
            $products[$key]['currency'] = $items['currency'][$key];
        }
        $create->productOffers()->createMany($products);
        /*foreach ($products as $product) {
            ProductOffer::create($product);
        }*/
        if ($create)
            return redirect()->route('admin.offer.index')->with('success', 'Teklif başarıyla eklendi.');
        else
            return redirect()->back()->with('error', 'Teklif eklenirken bir hata oluştu.');
    }

    public function show(Offer $offer)
    {
        /** @var Offer $offer  */
        $offer = Offer::query()
            ->where('id', $offer->id)
            ->with(['customer', 'delivery', 'productTag', 'productOffers'])
            ->firstOrFail();

        $fileName = 'offer-' . $offer->id . '.pdf';
        $data = [
            'offer' => $offer
        ];

       return view('admin.offer.pdf', compact('offer'));
        /** @var Dompdf $pdf */
    /*    $pdf = Pdf::loadView('admin.offer.pdf',['data' => $data]);
//        $pdf->setBasePath(public_path('assets/css/'));
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'max_execution_time' => 300]);

        return $pdf->download('test.pdf');*/
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {

        $productTags = ProductTag::all();
        $term_of_offers = SystemTermOfOffer::all();
        $deliveries = SystemDeliveryMethod::all();
        $customers = Customer::where('personal_type', $offer->customer->personal_type)->get();
        $products = Product::where('product_tag_id', $offer->product_tag_id)->get();
        //dd($offer->products);
        return view('admin.offer.edit', compact('offer', 'productTags', 'term_of_offers', 'deliveries', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OfferStoreRequest $request, Offer $offer)
    {
        $data = collect($request->validated());

        $items = ($data['products']);
        unset($data['products']);

        $update = $offer->update($data->toArray());

        $products = [];
        foreach ($items['name'] as $key => $name) {

            $products[$key]['product_id'] = $items['product_id'][$key];
            $products[$key]['offer_id'] = $offer->id;
            $products[$key]['name'] = $items['name'][$key];
            $products[$key]['category'] = $items['category'][$key];
            $products[$key]['product_size'] = $items['product_size'][$key];
            $products[$key]['type'] = $items['type'][$key];
            $products[$key]['material'] = $items['material'][$key];
            $products[$key]['color'] = $items['color'][$key];
            $products[$key]['detail'] = $items['detail'][$key];
            $products[$key]['quantity'] = (int)$items['quantity'][$key];
            $products[$key]['price'] = (float)$items['price'][$key];
            $products[$key]['currency'] = $items['currency'][$key];
        }
        ProductOffer::query()
            ->where('offer_id', $offer->id)
            ->delete();
        foreach ($products as $product) {
//            dd($product);
            ProductOffer::create($product);
        }

        if ($update)
            return redirect()->route('admin.offer.index')->with('success', 'Teklif başarıyla güncellendi.');
        else
            return redirect()->back()->with('error', 'Teklif güncellenirken bir hata oluştu.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        if ($offer->delete())
            return response()->json(['status' => true, 'message' => 'Teklif başarıyla silindi.']);
        return response()->json(['status' => false, 'message' => 'Teklif silinirken bir hata oluştu.']);
    }

    /**
     * get customer
     */
    public function getCustomer(Request $request)
    {
        $data = $request->validate([
            'offer_type' => ['required', new Enum(CustomerPersonalTypeEnum::class)]
        ]);
        $customer = Customer::where('personal_type', $data['offer_type'])->get();

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found'], 404);
        }
        return response()->json($customer);
    }

    /**
     * get product
     */
    public function getProduct(Request $request)
    {
        $data = $request->validate([
            'product_tag' => ['required', 'exists:product_tags,id']
        ]);

        $products = Product::query()
            ->where('product_tag_id', $data['product_tag'])
            ->with(['category', 'productTag', 'currency'])
            ->get();
//        dd($products[0]->type[0]);
        if (!$products) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }
        return response()->json($products);
    }
}
