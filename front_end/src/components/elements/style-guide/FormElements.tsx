"use client"
import React from 'react';
import NiceSelect from '../nice-select/NiceSelect';
const selectOption = [
    { id: 1, option: "Select option One" },
    { id: 2, option: "Select option Two" },
    { id: 3, option: "Select option Three" }
]


const FormElements = () => {
    const selectHandler = () => { }
    return (
        <>
            <div className="bd-style-guide-form section-space-small-bottom">
                <h5 className="bd-style-guide-title mb-20">Form Elements</h5>
                <div className="bd-style-form-input">
                    <div className="row gy-30">
                        <div className="col-lg-6">
                            <div className="form-input-box">
                                <div className="form-input">
                                    <input name="name" id="firstName" type="text" placeholder="First name" />
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box">
                                <div className="form-input">
                                    <input name="name" id="lastName" type="text" placeholder="Last name" />
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="firstName3">First name<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="name" id="firstName3" type="text" placeholder="First name" />
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="lastName2">Last name<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="name" id="lastName2" type="text" placeholder="Last name" />
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="floating-form-input">
                                <input type="text" className="inputText" />
                                <span className="floating-label">Full Name</span>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="floating-form-input">
                                <input type="text" className="inputText" />
                                <span className="floating-label">Your Email</span>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box has-icon icon-right">
                                <div className="form-input">
                                    <input name="name2" type="text" placeholder="Your Name" />
                                    <div className=""><span><i className="fa-solid fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box has-icon icon-right">
                                <div className="form-input">
                                    <input name="email2" type="text" placeholder="Your Email" />
                                    <div className=""><span><i className="fa-solid fa-envelope"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box has-icon icon-left">
                                <div className="form-input">
                                    <input name="name2" type="text" placeholder="Your Name" />
                                    <div className=""><span><i className="fa-solid fa-user"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="form-input-box has-icon icon-left">
                                <div className="form-input">
                                    <input name="email2" type="text" placeholder="Your Email" />
                                    <div className=""><span><i className="fa-solid fa-envelope"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-12">
                            <div className="form-input-box">
                                <div className="form-input">
                                    <textarea name="name" placeholder="Your Message"></textarea>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-12">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label>Your Message<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <textarea name="name" placeholder="Your Message"></textarea>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-12">
                            <div className="form-input-box has-icon icon-right">
                                <div className="form-input">
                                    <textarea name="message" placeholder="Your Message"></textarea>
                                    <div className=""><span><i className="fa-solid fa-pen"></i></span></div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-12">
                            <div className="form-input-box has-icon icon-left">
                                <div className="form-input">
                                    <textarea name="message" placeholder="Your Message"></textarea>
                                    <div className=""><span><i className="fa-solid fa-pen"></i></span></div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-12">
                            <div className="form-input-box">
                                <div className="floating-form-input">
                                    <textarea className="textareaText"></textarea>
                                    <span className="floating-label-two">Your Message</span>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-12">
                            <div className="row gy-30">
                                <div className="col-lg-6">
                                    <div className="form-input">
                                        <div className="form-input-title">
                                            <label> Default select option<span>*</span></label>
                                        </div>
                                        <NiceSelect
                                            options={selectOption}
                                            defaultCurrent={0}
                                            onChange={selectHandler}
                                            filterIcon={false}
                                            name=""
                                            className=""
                                        />
                                    </div>
                                </div>
                                <div className="col-lg-6">
                                    <div className="form-input">
                                        <div className="form-input-title mb--5">
                                            <label>Nice select option</label>
                                        </div>
                                        <div className="input-box-select">
                                            <NiceSelect
                                                options={selectOption}
                                                defaultCurrent={0}
                                                onChange={selectHandler}
                                                filterIcon={false}
                                                name=""
                                                className=""
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-4">
                            <h4 className="mb-20">Radio Button</h4>
                            <div className="singel__radio mb-15">
                                <input type="radio" id="back_transfer" name="payment" />
                                <label htmlFor="back_transfer" data-bs-toggle="direct-bank-transfer">Direct
                                    Bank
                                    Transfer</label>
                            </div>
                            <div className="singel__radio">
                                <input type="radio" id="cheque_payment" name="payment" />
                                <label htmlFor="cheque_payment">Cheque Payment</label>
                            </div>
                        </div>
                        <div className="col-lg-4">
                            <h4 className="mb-20">Checkbox Button</h4>
                            <div className="checkout-option mb-15">
                                <input id="checkboxDefault" type="checkbox" />
                                <label htmlFor="checkboxDefault">Checkbox</label>
                            </div>
                            <div className="checkout-option">
                                <input id="checkboxChecked" type="checkbox" defaultChecked />
                                <label htmlFor="checkboxChecked">checked</label>
                            </div>
                        </div>
                        <div className="col-lg-4">
                            <h4 className="mb-20">Switch checkbox</h4>
                            <div className="singel__switch d-flex-items gap-30 mb-15">
                                <label className="switch-checkbox">
                                    <input type="checkbox" />
                                    <span className="switch round"></span>
                                </label>
                                <p>Default Switch</p>
                            </div>
                            <div className="singel__switch d-flex-items gap-30">
                                <label className="switch-checkbox">
                                    <input type="checkbox" />
                                    <span className="switch round"></span>
                                </label>
                                <p>Selected Switch</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default FormElements;