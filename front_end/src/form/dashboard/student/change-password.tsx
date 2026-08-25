import ErrorMsg from '@/form/auth/ErrorMsg';
import React from 'react';
import { useForm } from 'react-hook-form';
import { toast } from 'sonner';

type FormData = {
    studentCurrentPassword: string;
    studentNewPassword: string;
    studentConfirmPassword: string;
};

const ChangePasswordForm = () => {
    const {
        register,
        handleSubmit,
        watch,
        formState: { errors }, reset
    } = useForm<FormData>();

    const onSubmit = () => {
        toast.success('Password updated successfully');
        reset()
    };
    return (
        <>
            <form onSubmit={handleSubmit(onSubmit)} className="row g-30">
                <div className="col-lg-12">
                    <div className="form-group">
                        <input
                            {...register("studentCurrentPassword", { required: "Current Password is required" })}
                            id="studentPassword"
                            type="password"
                            placeholder="Current Password"
                        />
                        <ErrorMsg error={errors?.studentCurrentPassword?.message} />
                    </div>
                </div>

                <div className="col-lg-12">
                    <div className="form-group">
                        <input
                            {...register("studentNewPassword", {
                                required: "New Password is required",
                                minLength: { value: 8, message: "Password must be at least 8 characters" },
                            })}
                            id="studentNewPassword"
                            type="password"
                            placeholder="New Password"
                        />
                        <ErrorMsg error={errors?.studentNewPassword?.message} />
                    </div>
                </div>

                <div className="col-lg-12">
                    <div className="form-group">
                        <input
                            {...register("studentConfirmPassword", {
                                required: "Confirm Password is required",
                                validate: (value) =>
                                    value === watch("studentNewPassword") || "Passwords do not match",
                            })}
                            id="studentConfirmPassword"
                            type="password"
                            placeholder="Confirm Password"
                        />
                        <ErrorMsg error={errors?.studentConfirmPassword?.message} />
                    </div>
                </div>

                <div className="col-lg-12">
                    <button type="submit" className="bd-btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </>
    );
};

export default ChangePasswordForm;