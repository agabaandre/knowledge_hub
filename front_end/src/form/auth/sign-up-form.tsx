"use client";
import React from "react";
import { useForm } from "react-hook-form";
import ErrorMsg from "./ErrorMsg";
import Link from "next/link";

type SignUpFormData = {
    name: string;
    email: string;
    password: string;
};

const SignUpForm = () => {
    const { register, handleSubmit, formState: { errors } } = useForm<SignUpFormData>();

    const onSubmit = (data: SignUpFormData) => {
        console.log("Sign Up Successful:", data);
    };

    return (
        <>
            <form onSubmit={handleSubmit(onSubmit)}>
                {/* Full Name Field */}
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="fullName">Full Name <span>*</span></label>
                    </div>
                    <div className="form-input">
                        <input
                            {...register("name", { 
                                required: "Full Name is required", 
                                minLength: { value: 3, message: "Name must be at least 3 characters" }
                            })}
                            id="fullName"
                            type="text"
                            placeholder="Full Name"
                        />
                        <ErrorMsg error={errors.name?.message} />
                    </div>
                </div>

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

                {/* Password Field */}
                <div className="form-input-box mb-20">
                    <div className="form-input-title">
                        <label htmlFor="password">Password <span>*</span></label>
                    </div>
                    <div className="form-input">
                        <input
                            {...register("password", { 
                                required: "Password is required", 
                                minLength: { value: 6, message: "Password must be at least 6 characters" }
                            })}
                            id="password"
                            type="password"
                            placeholder="Your Password"
                            autoComplete="true"
                        />
                        <ErrorMsg error={errors.password?.message} />
                    </div>
                </div>

                {/* Remember Me & Forgot Password */}
                <div className="d-flex-between flex-wrap mb-20">
                    <div className="checkout-option">
                        <input id="rememberMe" type="checkbox" />
                        <label htmlFor="rememberMe">Remember me</label>
                    </div>
                    <div className="sign-forgot underline">
                        <Link href="/forgot" className="sign-link">Forgot Password?</Link>
                    </div>
                </div>

                {/* Submit Button */}
                <div className="bd-sign-btn">
                    <button className="bd-btn btn-primary w-100" type="submit">
                        Create Account
                    </button>
                </div>
            </form>
        </>
    );
};

export default SignUpForm;
