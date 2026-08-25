"use client"
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import BillingDetailsForm from '@/form/checkout/billing-details-form';
import React, { useState } from 'react';
import CouponCode from '../../../../form/checkout/coupon-code';
import CustomerLogin from '@/form/checkout/customer-login-form';
import CheckoutPayment from './CheckoutPayment';
import SlideToggleTwo from '@/utils/SlideToggleTwo';
import { AnimatePresence, motion } from 'framer-motion';
import { useSelector } from 'react-redux';
import { RootState } from '@/redux/store';
import { toast } from 'sonner';
import useGlobalContext from '@/hooks/useContexts';

const CheckoutMain = () => {
    const { toggleOpen, isOpen } = useGlobalContext();
    const [isCouponOpen, setIsCouponOpen] = useState<boolean>(false);
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
            <Breadcrumbs breadcrumbTitle='Checkout' />
            {/* -- checkout area start -- */}
            <section className="bd-shop-details-area section-space">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-7">
                            <div className="checkout-verify mb-30">
                                <div className="checkout-verify-item">
                                    <p className="checkout-verify-reveal">Returning customer?
                                        <button onClick={toggleOpen} type="button" className="checkout-login-form-reveal-btn">Click here to login</button>
                                    </p>
                                    <SlideToggleTwo>
                                        <div className={`return-customer ${isOpen ? 'd-block' : 'd-none'}`}>
                                            <CustomerLogin />
                                        </div>
                                    </SlideToggleTwo>
                                </div>
                                <div className="checkout-verify-item">
                                    <p className="checkout-verify-reveal">Have a coupon?
                                        <button onClick={() => setIsCouponOpen(!isCouponOpen)}
                                            type="button" className="checkout-coupon-form-reveal-btn">Click here to enter your code
                                        </button>
                                    </p>
                                    <AnimatePresence>
                                        {isCouponOpen && (
                                            <motion.div
                                                initial={{ height: 0, opacity: 0 }}
                                                animate={{ height: "auto", opacity: 1 }}
                                                exit={{ height: 0, opacity: 0 }}
                                                transition={{ duration: 0.6, ease: "easeInOut" }}>
                                                <div className={`return-customer ${isCouponOpen ? 'd-block' : 'd-none'}`}>
                                                    <CouponCode />
                                                </div>
                                            </motion.div>
                                        )}
                                    </AnimatePresence>
                                </div>
                            </div>
                            <div className="checkout-bill-area">
                                <h3 className="checkout-bill-title">Billing Details</h3>
                                <div className="checkout-bill-form">
                                    <BillingDetailsForm />
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-5">
                            {/* -- checkout place order -- */}
                            <div className="checkout-place sidebar-right sidebar-sticky">
                                <h3 className="checkout-place-title mb-20">Your Order</h3>
                                <div className="order-info-list">
                                    <ul>
                                        {/* -- header -- */}
                                        <li className="order-info-list-header">
                                            <h4>Product</h4>
                                            <h4>Total</h4>
                                        </li>
                                        {/* -- item list -- */}
                                        {cartProducts.map((item) => (
                                            <li className="order-info-list-desc" key={item.id}>
                                                <p>{item?.title}<span> x {item?.quantity}</span></p>
                                                <span>{item?.quantity && item?.price ? `$${(Number(item.quantity) * Number(item.price)).toFixed(2)}` : 'N/A'}</span>
                                            </li>
                                        ))}
                                        <li className="order-info-list-subtotal">
                                            <span>Subtotal</span>
                                            <span>${totalPrice.toFixed(2)}</span>
                                        </li>
                                        {/* -- shipping -- */}
                                        <li className="order-info-list-shipping">
                                            <span>Shipping</span>
                                            <div className="order-info-list-shipping-item d-flex flex-column align-items-start">
                                                <span>
                                                    <input onClick={() => setShippingCost(20)} id="flat_rate" type="radio" name="shipping" />
                                                    <label htmlFor="flat_rate">Flat rate: <span>$20.00</span></label>
                                                </span>
                                                <span>
                                                    <input onClick={() => setShippingCost(25)} id="local_pickup" type="radio" name="shipping" />
                                                    <label htmlFor="local_pickup">Local pickup: <span>$25.00</span></label>
                                                </span>
                                                <span>
                                                    <input onClick={() => setShippingCost(0)} id="free_shipping" type="radio" name="shipping" />
                                                    <label htmlFor="free_shipping">Free shipping</label>
                                                </span>
                                            </div>
                                        </li>
                                        {/* -- total -- */}
                                        <li className="order-info-list-total">
                                            <span>Total</span>
                                            <span>${(totalPrice + shippingCost).toFixed(2)}</span>
                                        </li>
                                    </ul>
                                </div>
                                <CheckoutPayment />
                                <div className="checkout-agree">
                                    <div className="checkout-option mb-15">
                                        <input id="read_all" type="checkbox" />
                                        <label htmlFor="read_all">I have read and agree to the website.</label>
                                    </div>
                                </div>
                                <div className="checkout-btn-wrapper">
                                    <button onClick={() => toast.success("Your order has been placed successfully! 🎉")} type="submit" className="bd-btn btn-outline-primary">Place Order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- checkout area end -- */}
        </>
    );
};

export default CheckoutMain;