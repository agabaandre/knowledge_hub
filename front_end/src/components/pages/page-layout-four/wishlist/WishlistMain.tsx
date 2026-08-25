"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import { ProductsType } from '@/interFace/interFace';
import { cart_wishlist_product } from '@/redux/slices/cartSlice';
import { remove_wishlist_product } from '@/redux/slices/wishlistSlice';
import { RootState } from '@/redux/store';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';

const WishlistMain = () => {
    const dispatch = useDispatch();
    const wishlistProducts = useSelector(
        (state: RootState) => state.wishlist.cartProducts
    );

    const handleAddToCart = (product: ProductsType) => {
        const modifyProduct = wishlistProducts.find(
            (item) => item.id === product?.id
        );
        if (modifyProduct) {
            dispatch(cart_wishlist_product(modifyProduct));
        }
    };
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Wishlist' />
            {/* -- wishlist area start -- */}
            <section className="bd-shop-details-area section-space">
                <div className="container">
                    {wishlistProducts?.length ? (
                        <>
                            <div className="row">
                                <div className="col-12">
                                    <div className="shop-table-content table-responsive">
                                        <table className="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style={{ minWidth: "172px" }}>Images</th>
                                                    <th style={{ minWidth: "305px" }}>Product</th>
                                                    <th>Unit Price</th>
                                                    <th>Add to cart</th>
                                                    <th>Total</th>
                                                    <th style={{ minWidth: "188px" }}>Remove</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {wishlistProducts?.map((item, index) => (
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
                                                            <td>${item.price ? item.price.toFixed(2) : "0.00"}</td>
                                                            <td><button
                                                                onClick={(e) => {
                                                                    e.preventDefault();
                                                                    handleAddToCart(item);
                                                                }}
                                                                className="bd-btn btn-primary">Add to Cart</button>
                                                            </td>
                                                            <td>{`$${item.price?.toFixed(2) ?? 0}`}</td>
                                                            <td><button onClick={() => dispatch(remove_wishlist_product(item))} className="removeRow"><i className="fa fa-times"></i> Remove</button></td>
                                                        </tr>
                                                    ) : (
                                                        <tr key={index}>
                                                            <td className="product-thumbnail">
                                                                <Link href={`/shop/shop-details/${item.id}`}>
                                                                    {item.image && <Image  src={item.image} alt="img" />}
                                                                </Link>
                                                            </td>

                                                            <td><Link href={`/shop/shop-details/${item.id}`}>{item?.title}</Link></td>

                                                            <td>${item.price ? item.price.toFixed(2) : "0.00"}</td>
                                                            <td><button
                                                                onClick={(e) => {
                                                                    e.preventDefault();
                                                                    handleAddToCart(item);
                                                                }}
                                                                className="bd-btn btn-primary">Add to Cart</button>
                                                            </td>
                                                            <td>{`$${item.price?.toFixed(2) ?? 0}`}</td>
                                                            <td><button onClick={() => dispatch(remove_wishlist_product(item))} className="removeRow"><i className="fa fa-times"></i> Remove</button></td>
                                                        </tr>
                                                    )
                                                ))
                                                }
                                            </tbody>
                                        </table>
                                    </div>
                                    <div className="text-center mt-50">
                                        <Link className="bd-btn btn-outline-primary" href="/cart">Go To Cart</Link>
                                    </div>
                                </div>
                            </div>
                        </>
                    ) : (
                        <div className="text-center">
                            <h3>Your wishlist is empty</h3>
                        </div>
                    )}
                </div>
            </section>
            {/* -- wishlist area end -- */}
        </>
    );
};

export default WishlistMain;