"use client"
import { RootState } from '@/redux/store';
import Link from 'next/link';
import React, { useState } from 'react';
import { useSelector } from 'react-redux';

const CardCheckout = () => {
    const [shippingCost, setShippingCost] = useState(0);
    const cartProducts = useSelector(
        (state: RootState) => state.cart.cartProducts
    );
    const totalPrice = cartProducts.reduce((total, product) => {
        if (typeof product.price === 'number' && product.price !== 0) {
            return total + (product.price ?? 0) * (product.quantity ?? 0);
        }
        return total;
    }, 0);
    return (
        <>
            <div className="bd-cart-checkout-wrapper wow bdFadeInRight" data-wow-delay=".4s">
                <div className="bd-cart-checkout-top d-flex align-items-center justify-content-between">
                    <span className="bd-cart-checkout-top-title">Subtotal</span>
                    <span className="bd-cart-checkout-top-price">${totalPrice.toFixed(2)}</span>
                </div>
                <div className="bd-cart-checkout-shipping">
                    <h4 className="bd-cart-checkout-shipping-title">Shipping</h4>
                    <div className="bd-cart-checkout-shipping-option-wrapper">
                        <div className="bd-cart-checkout-shipping-option">
                            <input onClick={() => setShippingCost(20)} id="flat_rate" type="radio" name="shipping" />
                            <label htmlFor="flat_rate">Flat rate: <span>$20.00</span></label>
                        </div>
                        <div className="bd-cart-checkout-shipping-option">
                            <input onClick={() => setShippingCost(25)} id="local_pickup" type="radio" name="shipping" />
                            <label htmlFor="local_pickup">Local pickup: <span> $25.00</span></label>
                        </div>
                        <div className="bd-cart-checkout-shipping-option">
                            <input onClick={() => setShippingCost(0)} id="free_shipping" type="radio" name="shipping" />
                            <label htmlFor="free_shipping">Free shipping</label>
                        </div>
                    </div>
                </div>
                <div className="bd-cart-checkout-total d-flex align-items-center justify-content-between">
                    <span>Total</span>
                    <span>${(totalPrice + shippingCost).toFixed(2)}</span>
                </div>
                <div className="bd-cart-checkout-proceed text-center">
                    <Link className="bd-btn btn-primary" href="/checkout"><span className="text">Proceed to
                        Checkout</span></Link>
                </div>
            </div>
        </>
    );
};

export default CardCheckout;