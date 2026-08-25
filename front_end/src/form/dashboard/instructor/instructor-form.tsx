"use client";

import ErrorMsg from '@/form/auth/ErrorMsg';
import React from 'react';
import { useForm, SubmitHandler } from 'react-hook-form';
import { toast } from 'sonner';

// Define the form data type
interface InstructorSettingsFormData {
    firstName: string;
    lastName: string;
    userName: string;
    phoneNumber: string;
    skillName: string;
    instructorGmail: string;
    contactMessage: string;
}

const InstructorForm = () => {
    const { register, handleSubmit, formState: { errors }, reset } = useForm<InstructorSettingsFormData>();

    const onSubmit: SubmitHandler<InstructorSettingsFormData> = () => { 
        toast.success('Profile updated successfully');
        reset();
    };

    return (
        <>
            <form onSubmit={handleSubmit(onSubmit)} className="bd-dashboard-profile-inner">
                <div className="row gy-30">
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("firstName", { required: "First Name is required" })}
                                type="text" placeholder="First Name"
                            />
                            <ErrorMsg error={errors?.firstName?.message} />
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("lastName", { required: "Last Name is required" })}
                                type="text" placeholder="Last Name"
                            />
                            <ErrorMsg error={errors?.lastName?.message} />
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("userName", { required: "User Name is required" })}
                                type="text" placeholder="User Name"
                            />
                            <ErrorMsg error={errors?.userName?.message} />
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("phoneNumber", {
                                    required: "Phone Number is required",
                                    pattern: { value: /^[0-9]+$/, message: "Invalid phone number" }
                                })}
                                type="tel" placeholder="Phone Number"
                            />
                            <ErrorMsg error={errors?.phoneNumber?.message} />
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("skillName", { required: "Skill/Occupation is required" })}
                                type="text" placeholder="Skill/Occupation"
                            />
                            <ErrorMsg error={errors?.skillName?.message} />
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("instructorGmail", {
                                    required: "Email is required",
                                    pattern: { value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: "Invalid email format" }
                                })}
                                type="text" placeholder="info@gmail.com"
                            />
                            <ErrorMsg error={errors?.instructorGmail?.message} />
                        </div>
                    </div>
                    <div className="col-lg-12">
                        <div className="form-group">
                            <textarea
                                {...register("contactMessage", { required: "Bio is required" })}
                                placeholder="Your Bio"
                            ></textarea>
                            <ErrorMsg error={errors?.contactMessage?.message} />
                        </div>
                    </div>
                    <div className="col-lg-12">
                        <button type="submit" className="bd-btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </>
    );
};

export default InstructorForm;