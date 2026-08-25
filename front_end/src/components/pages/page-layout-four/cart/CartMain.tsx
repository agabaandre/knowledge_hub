"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import React from 'react';
import CardCheckout from './CardCheckout';
import CartCouponCodeForm from '@/form/CartCouponCodeForm';
import { useDispatch, useSelector } from 'react-redux';
import { RootState } from '@/redux/store';
import { cart_product, clear_cart, decrease_quantity, remove_cart_product } from '@/redux/slices/cartSlice';
import { ProductsType } from '@/interFace/interFace';
import Link from 'next/link';
import Image from 'next/image';

const CartMain = () => {
    const dispatch = useDispatch();
    const cartProducts = useSelector(
        (state: RootState) => state.cart.cartProducts
    );
    const removeAllProduct = () => {
        dispatch(clear_cart());
    };

    const handleAddToCart = (product: ProductsType) => {
        dispatch(cart_product(product));
    };

    const handDecressCart = (product: ProductsType) => {
        dispatch(decrease_quantity(product));
    };

    const handleDelteProduct = (product: ProductsType) => {
        dispatch(remove_cart_product(product));
    };
    const handleChange = () => { };

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Cart' />
            {/* -- Cart area start -- */}

            {cartProducts.length === 0 &&
                <div className="container">
                    <div className="empty-text pt-100 pb-100 text-center">
                        <h3>Your cart is empty</h3>
                    </div>
                </div>
            }
            {cartProducts.length >= 1 &&
                <section className="bd-shop-details-area section-space">
                    <div className="container">
                        <div className="row gy-30">
                            <div className="col-xl-9 col-lg-8 wow bdFadeInLeft" data-wow-delay=".3s">
                                <div className="bd-cart-list mb-25 mr-30">
                                    <div className="shop-table-content table-responsive">
                                        <table className="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style={{ minWidth: "140px" }}>Images</th>
                                                    <th style={{ minWidth: "250px" }}>Product</th>
                                                    <th>Unit Price</th>
                                                    <th>Quantity</th>
                                                    <th>Remove</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {
                                                    cartProducts.map((item, index) => (

                                                        item.instructorImage ? (
                                                            <tr key={index}>
                                                                <td className="product-thumbnail">
                                                                    <div className=" d-flex justify-content-center" key={item.id}>
                                                                        <div className="bd-sidebar-cart-thumb product-cart-thumb">
                                                                            <Link href={`/shop/shop-details/${item.id}`}>
                                                                                {item.image && (
                                                                                    <div className='bd-sidebar-cart-instructor-image'>
                                                                                        <Image src={item.image} style={{ width: "100%", height: "auto" }} className='instructor-bg' alt="Image" />
                                                                                        <div className='instructor-image cart-instructor-image'>
                                                                                            <Image src={item.instructorImage} style={{ width: "100%", height: "auto" }} className='instructor-bg' alt="Image" />
                                                                                        </div>
                                                                                    </div>
                                                                                )}
                                                                            </Link>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td><Link href={`/shop/shop-details/${item.id}`}>{item?.title}</Link></td>
                                                                <td>{`$${item.price?.toFixed(2)}`}</td>
                                                                <td>
                                                                    <div className="bd-product-quantity">
                                                                        <span onClick={() => handDecressCart(item)} className="decrease">
                                                                            <i className="fa-regular fa-minus"></i>
                                                                        </span>
                                                                        <input className="bd-product-quantity-input" type="text" onChange={handleChange} value={item.quantity} readOnly />
                                                                        <span onClick={() => handleAddToCart(item)} className="increase">
                                                                            <i className="fa-regular fa-plus"></i>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <button onClick={() => handleDelteProduct(item)} className="removeRow"><i className="fa fa-times"></i> Remove</button>
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            <tr key={index}>
                                                                <td className="product-thumbnail"><Link href={`/shop/shop-details/${item.id}`}>
                                                                    {item.image && <Image src={item.image} alt="img" />}
                                                                </Link></td>
                                                                <td><Link href={`/shop/shop-details/${item.id}`}>{item?.title}</Link></td>
                                                                <td>{`$${item.price?.toFixed(2)}`}</td>
                                                                <td>
                                                                    <div className="bd-product-quantity">
                                                                        <span onClick={() => handDecressCart(item)} className="decrease">
                                                                            <i className="fa-regular fa-minus"></i>
                                                                        </span>
                                                                        <input className="bd-product-quantity-input" type="text" onChange={handleChange} value={item.quantity} readOnly />
                                                                        <span onClick={() => handleAddToCart(item)} className="increase">
                                                                            <i className="fa-regular fa-plus"></i>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <button onClick={() => handleDelteProduct(item)} className="removeRow"><i className="fa fa-times"></i> Remove</button>
                                                                </td>
                                                            </tr>
                                                        )
                                                    ))
                                                }
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div className="bd-cart-bottom mr-30">
                                    <div className="row gy-30 align-items-end">
                                        <div className="col-xl-7 col-md-8">
                                            <div className="bd-cart-coupon">
                                                <CartCouponCodeForm />
                                            </div>
                                        </div>
                                        <div className="col-xl-5 col-md-4">
                                            <div className="bd-cart-update text-md-end">
                                                <button type="submit" className="bd-btn btn-secondary" onClick={removeAllProduct}>Clear Cart</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xl-3 col-lg-4 col-md-6">
                                <CardCheckout />
                            </div>
                        </div>
                    </div>
                </section>
            }
            {/* -- Cart area end -- */}
        </>
    );
};

export default CartMain;