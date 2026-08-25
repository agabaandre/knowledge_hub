import Link from 'next/link';
import React from 'react';

const CustomerLogin = () => {
    return (
        <>
            <form action="#">
                <div className="return-customer-input">
                    <label>Email</label>
                    <input type="text" placeholder="Your Email" />
                </div>
                <div className="return-customer-input">
                    <label>Password</label>
                    <input type="password" placeholder="Password" />
                </div>
                <div className="return-customer-suggetions d-sm-flex align-items-center justify-content-between mb-20">
                    <div className="return-customer-remember">
                        <input id="remember" type="checkbox" />
                        <label htmlFor="remember">Remember me</label>
                    </div>
                    <div className="return-customer-forgot">
                        <Link href="/forgot">Forgot Password?</Link>
                    </div>
                </div>
                <Link href="/sign-in" className="bd-btn btn-primary">Log In</Link>
            </form>
        </>
    );
};

export default CustomerLogin;