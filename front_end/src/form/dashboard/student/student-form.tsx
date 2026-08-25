import ErrorMsg from '@/form/auth/ErrorMsg';
import React from 'react';
import { useForm } from 'react-hook-form';
import { toast } from 'sonner';

interface FormValues {
    studentFirstName: string;
    studentLastName: string;
    studentUserName: string;
    studentPhoneNumber: string;
    studentSkill?: string;
    studentGmail: string;
    contactMessage?: string;
}

const StudentForm = () => {
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<FormValues>();

    const onSubmit = () => {
        toast.success('Profile updated successfully');
    };
    return (
        <>
            <form onSubmit={handleSubmit(onSubmit)}>
                <div className="row gy-30">
                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("studentFirstName", { required: "First Name is required" })}
                                type="text"
                                placeholder="First Name"
                            />
                            <ErrorMsg error={errors?.studentFirstName?.message} />
                        </div>
                    </div>

                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("studentLastName", { required: "Last Name is required" })}
                                type="text"
                                placeholder="Last Name"
                            />
                            <ErrorMsg error={errors?.studentLastName?.message} />
                        </div>
                    </div>

                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("studentUserName", { required: "Username is required" })}
                                type="text"
                                placeholder="User Name"
                            />
                            <ErrorMsg error={errors?.studentUserName?.message} />
                        </div>
                    </div>

                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("studentPhoneNumber", {
                                    required: "Phone Number is required",
                                    pattern: {
                                        value: /^[0-9]{10,15}$/,
                                        message: "Invalid phone number",
                                    },
                                })}
                                type="tel"
                                placeholder="Phone Number"
                            />
                            <ErrorMsg error={errors?.studentPhoneNumber?.message} />
                        </div>
                    </div>

                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("studentSkill")}
                                type="text"
                                placeholder="Skill/Occupation"
                            />
                        </div>
                    </div>

                    <div className="col-lg-6">
                        <div className="form-group">
                            <input
                                {...register("studentGmail", {
                                    required: "Email is required",
                                    pattern: {
                                        value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
                                        message: "Invalid email format",
                                    },
                                })}
                                type="email"
                                placeholder="info@gmail.com"
                            />
                            <ErrorMsg error={errors?.studentGmail?.message} />
                        </div>
                    </div>

                    <div className="col-lg-12">
                        <div className="form-group">
                            <textarea
                                {...register("contactMessage")}
                                placeholder="Your Bio"
                            ></textarea>
                        </div>
                    </div>

                    <div className="col-lg-12">
                        <button className="bd-btn btn-primary" type="submit">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </>
    );
};

export default StudentForm;