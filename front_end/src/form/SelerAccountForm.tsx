import Link from 'next/link';
import React from 'react';

const SelerAccountForm = () => {
    return (
        <>
            <form action="#">
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="emailAddress">Email Address <span>*</span></label>
                    </div>
                    <div className="form-input">
                        <input name="email" id="emailAddress" type="email" placeholder="Email Address" />
                    </div>
                </div>
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="password">Password <span>*</span></label>
                    </div>
                    <div className="form-input">
                        <input name="password" id="password" type="password" placeholder="Your Password" autoComplete='true'/>
                    </div>
                </div>
                <div className="d-flex-between flex-wrap mb-20">
                    <div className="checkout-option">
                        <input id="rememberMe" type="checkbox" />
                        <label htmlFor="rememberMe">Remember me</label>
                    </div>
                    <div className="sign-forgot underline">
                        <Link href="/forgot" className="sign-link">Forgot Password?</Link>
                    </div>
                </div>
                <div className="bd-sign-btn">
                    <button className="bd-btn btn-primary w-100" type="submit">Sign In</button>
                </div>
            </form>
        </>
    );
};

export default SelerAccountForm;