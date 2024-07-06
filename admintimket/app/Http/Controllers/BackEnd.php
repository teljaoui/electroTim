<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackEnd extends Controller
{
    public function Admin()
    {
        $products = Product::all()->count();
        $Ordes_Delivered = Order::where('statue', '>', 1)->count();
        $user = User::all();
        $orders = Order::where('statue', '<=', 0)->paginate(5);
        $ordersCount = Order::where('statue', '<=', 0)->count();
        $specials = Product::where('offer', '>', 0)->where('datefin', '>', now())->paginate(4);
        $orderenCoure = Order::where('statue', '=', 1)->count();
        return view('Admin', compact('Ordes_Delivered', 'products', 'specials', 'orders', 'user', 'orderenCoure', 'ordersCount'));
    }
    public function product()
    {
        $products = Product::with('categorie')->paginate(10);
        $categories = Categorie::all();
        return view('product', compact('products', 'categories'));

    }
    public function product_add()
    {
        $categories = Categorie::all();
        return view('product_add', compact('categories'));
    }
    public function product_update($id)
    {
        $product = Product::find($id);
        $categories = Categorie::all();
        $reviews = Review::where('product_id', $id)->get();
        return view('product_update', compact('product', 'categories', 'reviews'));
    }
    public function categories()
    {
        $categories = Categorie::paginate(10);
        return view('categories', compact('categories'));
    }
    public function categories_add()
    {
        return view('categories_add');
    }

    public function orders()
    {
        $orders = Order::where('statue', '<=', 0)->paginate(10);
        return view('orders', compact('orders'));
    }
    public function orders_confirme()
    {
        $orders = Order::where('statue', '=', 1)->paginate(10);
        return view('orders_confirme', compact('orders'));
    }
    public function orders_delivered()
    {
        $orders = Order::where('statue', '>', 1)->paginate(10);
        return view('orders_delivered', compact('orders'));
    }
    public function categorie_update($id)
    {
        $categorie = Categorie::find($id);
        return view('categorie_update', compact('categorie'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('users', compact('users'));
    }

    public function order_detail($id)
    {
        $order = Order::find($id);
        $order_details = Order_detail::where('order_id', $id)->get();
        $user = User::all();
        return view('order_detail', compact('order', 'order_details', 'user'));
    }
    public function categories_post(Request $request)
    {
        try {
            $title = str_replace(' ', '_', $request->get('title'));
            $file_name = uniqid() . "." . $request->file("img")->extension();

            $request->file("img")->move(public_path('images'), $file_name);

            Categorie::create([
                'title' => $title,
                'img' => 'images/' . $file_name,
                'quantités' => '0'
            ]);

            session()->flash('success', 'Catégorie ajoutée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout de la Catégorie. Assurez-vous de remplir tous les champs et de ne pas répéter les Catégories.');
        }

        return redirect('/categories');
    }


    public function categorie_up(Request $request)
    {
        $categorie = Categorie::find($request->get('id'));

        try {
            if ($request->hasFile('img')) {
                $file_name = $request->get('title') . "." . $request->file("img")->extension();

                $request->file("img")->move(public_path('images'), $file_name);

                $categorie->update([
                    'img' => 'images/' . $file_name,
                    'title' => $request->get('title'),
                    'quantités' => '0'
                ]);
            } else {
                $categorie->update([
                    'title' => $request->get('title'),
                    'quantités' => '0'
                ]);
            }

            session()->flash('success', 'Catégorie modifiée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la modification de la catégorie.');
        }

        return redirect('/categories');
    }

    public function categorie_delete($id)
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return redirect('/categories')->with('error', 'Catégorie non trouvée.');
        }

        $products = Product::where('categorie_id', $id)->get();
        foreach ($products as $product) {
            $product->delete();
        }
        $categorie->delete();

        return redirect('/categories')->with('success', 'La catégorie et tous les produits associés ont été supprimés avec succès.');
    }

    public function proudct_post(Request $request)
    {
        $offerPrice = $request->get('offerPrice');
        $Adimg = $request->file('Adimg');
        try {
            $file_name = uniqid() . "." . $request->file("img")->extension();
            $file_name2 = uniqid() . "." . $request->file("img")->extension();
            $file_name3 = uniqid() . "." . $request->file("img")->extension();
            $file_name4 = uniqid() . "." . $request->file("img")->extension();
            $request->file("img")->move(public_path('images'), $file_name);
            $request->file("img2")->move(public_path('images'), $file_name2);
            $request->file("img3")->move(public_path('images'), $file_name3);
            $request->file("img4")->move(public_path('images'), $file_name4);

            $productData = [
                'title' => $request->get('title'),
                'img' => 'images/' . $file_name,
                'img2' => 'images/' . $file_name2,
                'img3' => 'images/' . $file_name3,
                'img4' => 'images/' . $file_name4,
                'description' => $request->get('description'),
                'price' => $request->get('price'),
                'brand' => $request->get('brand'),
                'quantities' => $request->get('quantities'),
                'categorie_id' => $request->get('categorie_id'),
            ];

            if ($offerPrice > 0) {
                $price = $request->get('price');
                $productData['offer'] = 1;
                $productData['offerPrice'] = $offerPrice;
                $productData['percentage'] = (($price - $offerPrice) / $price) * 100;
                $productData['datefin'] = $request->get('datefin');
            } else {
                $productData['offer'] = null;
                $productData['offerPrice'] = null;
                $productData['percentage'] = null;
                $productData['datefin'] = null;
            }

            if ($Adimg && $Adimg->isValid()) {
                $ad_file_name = uniqid() . "." . $Adimg->extension();
                $Adimg->move(public_path('images'), $ad_file_name);

                $productData['Advertisement'] = 1;
                $productData['Adimg'] = 'images/' . $ad_file_name;
            } else {
                $productData['Advertisement'] = 0;
                $productData['Adimg'] = null;
            }

            Product::create($productData);

            $categorie = Categorie::find($request->get('categorie_id'));
            $categorie->quantités += 1;
            $categorie->save();
            session()->flash('success', 'Product ajouté avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout du Product. Assurez-vous de remplir tous les champs' . $e->getMessage());
        }
        return redirect('/product');
    }
    public function product_up(Request $request)
    {
        $product = Product::find($request->get('id'));
        $offerPrice = $request->get('offerPrice');
        $Adimg = $request->file('Adimg');
    
        try {
            $productData = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'price' => $request->get('price'),
                'brand' => $request->get('brand'),
                'quantities' => $request->get('quantities'),
                'review' => 4,
                'categorie_id' => $request->get('categorie_id'),
            ];
    
            if ($request->hasFile('img')) {
                $file_name = uniqid() . "." . $request->file("img")->extension();
                $request->file("img")->move(public_path('images'), $file_name);
                $productData['img'] = 'images/' . $file_name;
            }
    
            if ($request->hasFile('img2')) {
                $file_name2 = uniqid() . "." . $request->file("img2")->extension();
                $request->file("img2")->move(public_path('images'), $file_name2);
                $productData['img2'] = 'images/' . $file_name2;
            }
    
            if ($request->hasFile('img3')) {
                $file_name3 = uniqid() . "." . $request->file("img3")->extension();
                $request->file("img3")->move(public_path('images'), $file_name3);
                $productData['img3'] = 'images/' . $file_name3;
            }
    
            if ($request->hasFile('img4')) {
                $file_name4 = uniqid() . "." . $request->file("img4")->extension();
                $request->file("img4")->move(public_path('images'), $file_name4);
                $productData['img4'] = 'images/' . $file_name4;
            }
    
            if ($offerPrice > 0) {
                $request->validate([
                    'offerPrice' => 'required',
                    'datefin' => 'required',
                ]);
                $price = $request->get('price');
                $productData['offer'] = 1;
                $productData['offerPrice'] = $offerPrice;
                $productData['percentage'] = (($price - $offerPrice) / $price) * 100;
                $productData['datefin'] = $request->get('datefin');
            } else {
                $productData['offer'] = null;
                $productData['offerPrice'] = null;
                $productData['percentage'] = null;
                $productData['datefin'] = null;
            }
    
            if ($Adimg && $Adimg->isValid()) {
                $ad_file_name = uniqid() . "." . $Adimg->extension();
                $Adimg->move(public_path('images'), $ad_file_name);
                $productData['Advertisement'] = 1;
                $productData['Adimg'] = 'images/' . $ad_file_name;
            }
    
            $product->update($productData);
            session()->flash('success', 'Produit modifié avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la modification du produit. Assurez-vous de remplir tous les champs. ' . $e->getMessage());
        }
    
        return redirect('/product');
    }
    

    public function product_delete($id, $categorieid)
    {
        $product = Product::find($id);
        $categorie = Categorie::find($categorieid);
        $categorie->quantités -= 1;
        $categorie->save();
        if (!$product) {
            return redirect('/product')->with('error', 'Product non trouvée.');
        }
        $product->delete();

        return redirect('/product')->with('success', 'Product a été supprimée avec succès.');
    }

    public function publicite($id)
    {
        $product = Product::find($id);

        if ($product) {
            $product->Adimg = null;
            $product->Advertisement = 0;
            $product->save();
            return redirect('/product')->with('success', 'publicite est supprimé avec success');

        } else {
            return redirect('/product')->with('error', 'Product non trouvée.');
        }
    }

    public function confirme($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->statue = 1;
            $order->save();
        }
        return redirect('/order_detail' . '/' . $id)->with('success', 'Commande Statue  a été modfié avec succès.');
        ;
    }
    public function encoure($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->statue = 0;
            $order->save();
        }
        return redirect('/order_detail' . '/' . $id)->with('success', 'Commande Statue  a été modfié avec succès.');
        ;
    }
    public function delivered($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->statue = 2;
            $order->save();
        }
        return redirect('/order_detail' . '/' . $id)->with('success', 'Commande Statue  a été modfié avec succès.');
        ;
    }
    public function deleteOrder($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect('/orders')->with('error', 'Order non trouvée.');
        }
        $order->delete();
        return redirect('/orders')->with('success', 'order Supprimer avec succès.');
        ;

    }
    public function searchproduct(Request $request)
    {
        $search = Product::find($request->get('id'));
        return view('searchProduct', compact('search'));
    }
    public function searchordes(Request $request)
    {
        $search = Order::find($request->get('id'));
        return view('searchordes', compact('search'));
    }
    public function searchuser(Request $request)
    {
        $search = User::find($request->get('id'));
        return view('searchuser', compact('search'));
    }

    public function update_password()
    {
        $user = User::where('email', 'admin')->first();
        ;
        if (!$user) {
            session()->flash('error', 'Admin user not found.');
            return redirect('/update_password');
        }
        return view('auth.update_password', compact('user'));
    }
    public function password_up(Request $request)
    {
        $user = User::where('email', 'admin')->first();
        $password = $request->password;
        $password_confirme = $request->password_confirme;
        if ($password === $password_confirme) {
            $user->update(
                [
                    'password' => $password
                ]
            );
            session()->flash('success', 'Password Modifié avec succès.');
        } else {
            session()->flash('error', 'Passwords do not match');
        }
        return redirect('/update_password');
    }
    public function review_update($id)
    {
        $review = Review::find($id);
        return view('review_update', compact('review'));
    }
    public function review_up(Request $request)
    {
        $review = Review::find($request->id);
        if ($review) {
            $review->update(
                [
                    'email' => $request->email,
                    'review' => $request->review,
                    'content' => $request->content

                ]
            );
            session()->flash('success', 'review Modifié avec succès.');
        } else {
            session()->flash('error', 'review not Found');
        }
        return redirect('/product');
    }
    public function review_delete($id)
    {
        $review = Review::find($id);
        if ($review) {
            $review->delete();
            session()->flash('success', 'review Supprimé avec succès.');
        } else {
            session()->flash('error', 'review not Found');
        }
        return redirect('/product');
    }

    public function scanOffers()
    {
        $updatedRows = Product::where('datefin', '<', Carbon::now())
            ->update([
                'offer' => null,
                'offerPrice' => null,
                'percentage' => null,
                'datefin' => null
            ]);

        if ($updatedRows > 0) {
            return redirect('/product')->with('success', 'Offers have been scanned and updated successfully.');
        } else {
            return redirect('/product')->with('error', 'No products found with expired offers.');
        }
    }


}