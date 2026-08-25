import React, { useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { countryData } from '@/data/dropdown-data';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';
const BillingDetailsForm = () => {
    const [isPasswordOpen, setIsPasswordOpen] = useState<boolean>(false);
    const [isAddressOpen, setIsAddressOpen] = useState<boolean>(false);
    const selectHandler = () => { }
    return (
        <>
            <form action="#">
                <div className="checkout-bill-inner">
                    <div className="row">
                        <div className="col-md-6">
                            <div className="checkout-input">
                                <label>First Name <span>*</span></label>
                                <input type="text" placeholder="First Name" />
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="checkout-input">
                                <label>Last Name <span>*</span></label>
                                <input type="text" placeholder="Last Name" />
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input">
                                <label>Company name (optional)</label>
                                <input type="text" placeholder="Example LTD." />
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input">
                                <label>Country / Region </label>
                                <input type="text" placeholder="United States (US)" />
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input">
                                <label>Street address</label>
                                <input type="text" placeholder="House number and street name" />
                            </div>

                            <div className="checkout-input">
                                <input type="text" placeholder="Apartment, suite, unit, etc. (optional)" />
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input">
                                <label>Town / City</label>
                                <input type="text" placeholder="Town / City" />
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="checkout-input">
                                <label>State / County</label>
                                <NiceSelect
                                    options={countryData}
                                    defaultCurrent={0}
                                    onChange={selectHandler}
                                    filterIcon={false}
                                    name=""
                                    className="checkout-country country-list"
                                />
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="checkout-input">
                                <label>Postcode / ZIP</label>
                                <input type="text" placeholder="Postcode / ZIP" />
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input">
                                <label>Phone <span>*</span></label>
                                <input type="text" placeholder="Phone" />
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input">
                                <label>Email address <span>*</span></label>
                                <input type="email" placeholder="Email address" />
                            </div>
                        </div>

                        <div className="col-md-12">
                            <div className="checkout-option-wrapper create-acc">
                                <div className="checkout-option mb-15">
                                    <input id="cbox" type="checkbox" />
                                    <label onClick={() => setIsPasswordOpen(!isPasswordOpen)} htmlFor="cbox">Create an account?</label>
                                </div>
                                <AnimatePresence>
                                    {isPasswordOpen && (
                                        <motion.div
                                            initial={{ height: 0, opacity: 0 }}
                                            animate={{ height: "auto", opacity: 1 }}
                                            exit={{ height: 0, opacity: 0 }}
                                            transition={{ duration: 0.6, ease: "easeInOut" }}
                                            className='overflow-hidden'
                                        >
                                            <div className={`checkout-input create-account ${isPasswordOpen ? 'd-block' : 'd-none'}`}>
                                                <p>Create an account by entering the information below. If you
                                                    are a
                                                    returning
                                                    customer please login at the top of the page.</p>
                                                <label>Account Password <span>*</span></label>
                                                <input type="password" placeholder="Password" />
                                            </div>
                                        </motion.div>
                                    )}
                                </AnimatePresence>
                            </div>
                        </div>
                        <div className="different-address">
                            <div className="checkout-option mb-15">
                                <input id="ship-box" type="checkbox" />
                                <label onClick={() => setIsAddressOpen(!isAddressOpen)} htmlFor="ship-box">Ship to a different address?</label>
                            </div>
                            <AnimatePresence>
                                {isAddressOpen && (
                                    <motion.div
                                        initial={{ height: 0, opacity: 0 }}
                                        animate={{ height: "auto", opacity: 1 }}
                                        exit={{ height: 0, opacity: 0 }}
                                        transition={{ duration: 0.6, ease: "easeInOut" }}
                                        className='overflow-hidden'
                                    >
                                        <div className={`${isAddressOpen ? 'd-block' : 'd-none'}`}>
                                            <div className="row">
                                                <div className="col-md-12">
                                                    <div className="checkout-input">
                                                        <label>State / County</label>
                                                        <NiceSelect
                                                            options={countryData}
                                                            defaultCurrent={0}
                                                            onChange={selectHandler}
                                                            filterIcon={false}
                                                            name=""
                                                            className="checkout-country country-list"
                                                        />
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <div className="checkout-input">
                                                        <label>First Name <span>*</span></label>
                                                        <input type="text" placeholder="First Name" />
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <div className="checkout-input">
                                                        <label>Last Name <span>*</span></label>
                                                        <input type="text" placeholder="Last Name" />
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="checkout-input">
                                                        <label>Company Name</label>
                                                        <input type="text" placeholder="Company Name" />
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="checkout-input">
                                                        <label>Apartment/Suite No. <span>*</span></label>
                                                        <input type="text" placeholder="Street address" />
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="checkout-input">
                                                        <label>Address <span>*</span></label>
                                                        <input type="text" placeholder="Apartment, suite, unit etc. (optional)" />
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="checkout-input">
                                                        <label>Town / City <span>*</span></label>
                                                        <input type="text" placeholder="Town / City" />
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <div className="checkout-input">
                                                        <label>State / County <span>*</span></label>
                                                        <input type="text" placeholder="State / County" />
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <div className="checkout-input">
                                                        <label>Postcode / Zip <span>*</span></label>
                                                        <input type="text" placeholder="Postcode / Zip" />
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <div className="checkout-input">
                                                        <label>Email Address <span>*</span></label>
                                                        <input type="email" placeholder="Email address" />
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <div className="checkout-input">
                                                        <label>Phone <span>*</span></label>
                                                        <input type="text" placeholder="Postcode / Zip" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>
                        <div className="col-md-12">
                            <div className="checkout-input mb-0">
                                <label>Order notes (optional)</label>
                                <textarea placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form >
        </>
    );
};

export default BillingDetailsForm;