"use client";
import React from "react";
import { useForm } from "react-hook-form";
import ErrorMsg from "./ErrorMsg";

type ForgotFormData = {
    email: string;
};

const ForgotForm = () => {
    const { register, handleSubmit, formState: { errors } } = useForm<ForgotFormData>();

    const onSubmit = (data: ForgotFormData) => {
        console.log("Password Reset Email Sent:", data);
    };

    return (
        <>
            <form onSubmit={handleSubmit(onSubmit)}>
                {/* Email Field */}
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="emailAddress">Email Address <span>*</span></label>
                    </div>
                    <div className="form-input">
                        <input
                            {...register("email", { 
                                required: "Email is required", 
                                pattern: { value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, message: "Invalid email format" }
                            })}
                            id="emailAddress"
                            type="email"
                            placeholder="Email Address"
                        />
                        <ErrorMsg error={errors.email?.message} />
                    </div>
                </div>

                {/* Submit Button */}
                <div className="bd-sign-btn">
                    <button className="bd-btn btn-primary w-100" type="submit">
                        Reset Password
                    </button>
                </div>
            </form>
        </>
    );
};

export default ForgotForm;
