import Link from 'next/link';
import React from 'react';
import emtyImg from '../../../../public/assets/images/shop/empty-cart.webp';
import Image from 'next/image';
import { useDispatch, useSelector } from 'react-redux';
import { RootState } from '@/redux/store';
import { ProductsType } from '@/interFace/interFace';
import { remove_cart_product } from '@/redux/slices/cartSlice';

interface HeaderCartProps {
    setOpenCart: (isOpen: boolean) => void;
    openCart: boolean;
}

const SidebarCart: React.FC<HeaderCartProps> = ({ openCart, setOpenCart }) => {
    const dispatch = useDispatch();
    const handleRemoveCart = (product: ProductsType) => {
        dispatch(remove_cart_product(product));
    };
    const cartProducts = useSelector(
        (state: RootState) => state.cart.cartProducts
    );

    const totalPrice = cartProducts.reduce(
        (total, product) => total + (product.price ?? 0) * (product.quantity ?? 0),
        0
    );

    return (
        <>
            <div className={`bd-sidebar-cart-area ${openCart ? 'bd-sidebar-cart-opened' : ''}`}>
                <div className="bd-sidebar-cart-wrapper d-flex justify-content-between flex-column">
                    <div className="bd-sidebar-cart-top-wrapper">
                        <div className="bd-sidebar-cart-top p-relative">
                            <div className="bd-sidebar-cart-top-title">
                                <h5>Shopping cart</h5>
                            </div>
                            <div className="bd-sidebar-cart-close">
                                <button onClick={() => setOpenCart(false)} type="button" className="bd-sidebar-cart-close-btn cartmini-close-btn"><i
                                    className="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>
                        <div className="bd-sidebar-cart-widget">
                            {cartProducts.length > 0 ? (
                                cartProducts.map((item) => (
                                    <div key={item.id}>
                                        {
                                            item.instructorImage ? (
                                                <div className="bd-sidebar-cart-widget-item" key={item.id}>
                                                    <div className="bd-sidebar-cart-thumb">
                                                        <Link href={`/shop/shop-details/${item.id}`}>
                                                            {item.image && (
                                                                <div className='bd-sidebar-cart-instructor-image'>
                                                                    <Image src={item.image} style={{ width: "100%", height: "auto" }} className='instructor-bg' alt="Image" />
                                                                    <div className='instructor-image'>
                                                                        <Image src={item.instructorImage} style={{ width: "100%", height: "auto" }} className='instructor-bg' alt="Image" />
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </Link>
                                                    </div>
                                                    <div className="bd-sidebar-cart-content">
                                                        <h5 className="bd-sidebar-cart-title"><Link href={`/shop/shop-details/${item.id}`}>{item.title}</Link></h5>
                                                        <div className="bd-sidebar-cart-price-wrapper">
                                                            <span className="bd-sidebar-cart-price">{`$${Number(item.price || 0).toFixed(2)}`}</span>
                                                            <span className="bd-sidebar-cart-quantity">{" "}x{item.quantity}</span>
                                                        </div>
                                                    </div>
                                                    <button onClick={() => handleRemoveCart(item)} className="bd-sidebar-cart-del"><i className="fa-regular fa-xmark"></i></button>
                                                </div>
                                            ) : (
                                                <div className="bd-sidebar-cart-widget-item" key={item.id}>
                                                    <div className="bd-sidebar-cart-thumb">
                                                        <Link href={`/shop/shop-details/${item.id}`}>
                                                            {item.image && (
                                                                <Image className='thumb-size' src={item.image}  alt="Image" />
                                                            )}
                                                        </Link>
                                                    </div>
                                                    <div className="bd-sidebar-cart-content">
                                                        <h5 className="bd-sidebar-cart-title"><Link href={`/shop/shop-details/${item.id}`}>{item.title}</Link></h5>
                                                        <div className="bd-sidebar-cart-price-wrapper">
                                                            <span className="bd-sidebar-cart-price">{`$${Number(item.price || 0).toFixed(2)}`}</span>
                                                            <span className="bd-sidebar-cart-quantity">{" "}x{item.quantity}</span>
                                                        </div>
                                                    </div>
                                                    <button onClick={() => handleRemoveCart(item)} className="bd-sidebar-cart-del"><i className="fa-regular fa-xmark"></i></button>
                                                </div>
                                            )
                                        }
                                    </div>
                                ))
                            ) : (

                                <div className={`bd-sidebar-cart-empty text-center ${cartProducts.length === 0 ? '' : 'd-none'}`}>
                                    <Image src={emtyImg} style={{ width: '100%', height: 'auto' }} alt="Empty Cart" />
                                    <p>Your Cart is empty</p>
                                    <Link href="/shop" className="bd-btn btn-primary">Go to Shop</Link>
                                </div>
                            )}
                        </div>
                    </div>
                    {cartProducts.length > 0 && (
                        <div className="bd-sidebar-cart-checkout">
                            <div className="bd-sidebar-cart-checkout-title mb-30">
                                <h5>Subtotal:</h5>
                                <span>{`$${totalPrice.toFixed(2)}`}</span>
                            </div>
                            <div className="bd-sidebar-cart-checkout-btn">
                                <Link className="bd-btn btn-primary mb-15 w-100" href="/cart">View Cart</Link>
                                <Link className="bd-btn btn-secondary w-100" href="/checkout">Checkout</Link>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <div onClick={() => setOpenCart(false)} className={`body-overlay ${openCart ? 'opened' : ''}`}></div>
        </>
    );
};

export default SidebarCart;
