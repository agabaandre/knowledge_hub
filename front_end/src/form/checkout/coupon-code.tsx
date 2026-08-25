import React from 'react';

const CouponCode = () => {
    return (
        <>
            <form action="#">
                <div className="return-customer-input">
                    <label>Coupon Code :</label>
                    <input type="text" placeholder="Coupon" />
                </div>
                <button type="submit" className="bd-btn btn-success">Apply Coupon</button>
            </form>
        </>
    );
};

export default CouponCode;