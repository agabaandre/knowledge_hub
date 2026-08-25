"use client"
import Link from 'next/link';
import React from 'react';
import { useForm } from 'react-hook-form';
import ErrorMsg from './auth/ErrorMsg';

interface FormData {
    firstName: string;
    email: string;
    subject?: string;
    message: string;
}

const ContactForm: React.FC = () => {
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<FormData>();

    const onSubmit = () => {};

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <div className="row gy-30">
                {/* Full Name */}
                <div className="col-md-12">
                    <div className="form-input-box">
                        <div className="form-input-title">
                            <label htmlFor="firstName">Full Name<span>*</span></label>
                        </div>
                        <div className="form-input">
                            <input
                                {...register("firstName", { required: "Full Name is required" })}
                                id="firstName"
                                type="text"
                                placeholder="First Name"
                            />
                              <ErrorMsg error={errors?.firstName?.message} />
                        </div>
                    </div>
                </div>

                {/* Email Address */}
                <div className="col-md-12">
                    <div className="form-input-box">
                        <div className="form-input-title">
                            <label htmlFor="email">Email Address<span>*</span></label>
                        </div>
                        <div className="form-input">
                            <input
                                {...register("email", {
                                    required: "Email is required",
                                    pattern: {
                                        value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
                                        message: "Invalid email address",
                                    },
                                })}
                                id="email"
                                type="email"
                                placeholder="Email Address"
                            />
                            <ErrorMsg error={errors?.email?.message} />
                        </div>
                    </div>
                </div>

                {/* Subject (Optional) */}
                <div className="col-md-12">
                    <div className="form-input-box">
                        <div className="form-input-title">
                            <label htmlFor="subject">Subject</label>
                        </div>
                        <div className="form-input">
                            <input {...register("subject")} id="subject" type="text" placeholder="Subject" />
                        </div>
                    </div>
                </div>

                {/* Message */}
                <div className="col-xxl-12">
                    <div className="form-input-box mb-15">
                        <div className="form-input-title">
                            <label htmlFor="message">Message<span>*</span></label>
                        </div>
                        <div className="form-input">
                            <textarea
                                {...register("message", { required: "Message is required" })}
                                id="message"
                                placeholder="Message"
                            ></textarea>
                            <ErrorMsg error={errors?.message?.message} />
                        </div>
                    </div>

                    {/* Privacy Policy Checkbox */}
                    <div className="checkbox-option">
                        <input id="course-check-1" type="checkbox" />
                        <label htmlFor="course-check-1">
                            You agree to our friendly{" "}
                            <span className="text-border-highlights">
                                <Link href="/privacy-policy">privacy policy</Link>
                                <span className="theme-black h-1px bottom-0"></span>
                            </span>
                            .
                        </label>
                    </div>
                </div>

                {/* Submit Button */}
                <div className="col-xxl-12">
                    <div className="bd-contact-form-btn">
                        <button className="bd-btn btn-primary w-100" type="submit">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    );
};

export default ContactForm;
