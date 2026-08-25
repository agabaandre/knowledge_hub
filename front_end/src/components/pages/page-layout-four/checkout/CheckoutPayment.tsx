"use client"
import Image from 'next/image';
import React, { useState } from 'react';
import paymentOptionImg from '../../../../../public/assets/images/shape/payment-option.webp';
import Link from 'next/link';
import { AnimatePresence, motion } from 'framer-motion';

const CheckoutPayment = () => {
    const [isBankTransferOpen, setIsBankTransferOpen] = useState<boolean>(false);
    const [isCheckOutPayment, setIsCheckOutPayment] = useState<boolean>(false);
    const [isCheckoutPaymentDesc, setIsCheckoutPaymentDesc] = useState<boolean>(false);
    return (
        <>
            <div className="checkout-payment">
                <div className="checkout-payment-item">
                    <input type="radio" id="back_transfer" name="payment" />
                    <label onClick={() => setIsBankTransferOpen(!isBankTransferOpen)} htmlFor="back_transfer" data-bs-toggle="direct-bank-transfer">Direct Bank
                        Transfer</label>
                    <AnimatePresence>
                        {isBankTransferOpen && (
                            <motion.div
                                initial={{ height: 0, opacity: 0 }}
                                animate={{ height: "auto", opacity: 1 }}
                                exit={{ height: 0, opacity: 0 }}
                                transition={{ duration: 0.6, ease: "easeInOut" }}
                                className='overflow-hidden'
                            >
                                <div className={`checkout-payment-desc direct-bank-transfer ${isBankTransferOpen ? 'd-block' : 'd-none'}`}>
                                    <p>Make your payment directly into our bank account. Please use your Order ID as
                                        the
                                        payment reference. Your order will not be shipped until the funds have
                                        cleared in our
                                        account.</p>
                                </div>
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
                <div className="checkout-payment-item">
                    <input type="radio" id="cheque_payment" name="payment" />
                    <label onClick={() => setIsCheckOutPayment(!isCheckOutPayment)} htmlFor="cheque_payment">Cheque Payment</label>
                    <AnimatePresence>
                        {isCheckOutPayment && (
                            <motion.div
                                initial={{ height: 0, opacity: 0 }}
                                animate={{ height: "auto", opacity: 1 }}
                                exit={{ height: 0, opacity: 0 }}
                                transition={{ duration: 0.6, ease: "easeInOut" }}
                                className='overflow-hidden'
                            >
                                <div className={`checkout-payment-desc cheque-payment ${isCheckOutPayment ? 'd-block' : 'd-none'}`}>
                                    <p>Make your payment directly into our bank account. Please use your Order ID as
                                        the
                                        payment reference. Your order will not be shipped until the funds have
                                        cleared in our
                                        account.</p>
                                </div>
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
                <div className="checkout-payment-item">
                    <input type="radio" id="cod" name="payment" />
                    <label onClick={() => setIsCheckoutPaymentDesc(!isCheckoutPaymentDesc)} htmlFor="cod">Cash on Delivery</label>

                    <AnimatePresence>
                        {isCheckoutPaymentDesc && (
                            <motion.div
                                initial={{ height: 0, opacity: 0 }}
                                animate={{ height: "auto", opacity: 1 }}
                                exit={{ height: 0, opacity: 0 }}
                                transition={{ duration: 0.6, ease: "easeInOut" }}
                                className='overflow-hidden'
                            >

                                <div className={`checkout-payment-desc cash-on-delivery ${isCheckoutPaymentDesc ? 'd-block' : 'd-none'}`}>
                                    <p>Make your payment directly into our bank account. Please use your Order ID as
                                        the
                                        payment reference. Your order will not be shipped until the funds have
                                        cleared in our
                                        account.</p>
                                </div>
                            </motion.div>
                        )}
                    </AnimatePresence>



                </div>
                <div className="checkout-payment-item paypal-payment">
                    <input type="radio" id="paypal" name="payment" />
                    <label htmlFor="paypal">PayPal <Image src={paymentOptionImg} alt="image" /> <Link href="#">What is PayPal?</Link></label>
                </div>
            </div>
        </>
    );
};

export default CheckoutPayment;