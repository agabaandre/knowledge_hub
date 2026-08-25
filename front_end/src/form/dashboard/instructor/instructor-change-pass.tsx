"use client"
import ErrorMsg from '@/form/auth/ErrorMsg';
import React from 'react';
import { useForm } from 'react-hook-form';
import { toast } from 'sonner';

interface ChangePasswordFormData {
    currentPassword: string;
    newPassword: string;
    confirmPassword: string;
}

const InstructorChangePassForm = () => {
    const {
        register,
        handleSubmit,
        watch,
        formState: { errors },reset
    } = useForm<ChangePasswordFormData>();

    const onSubmit = () => { 
        toast.success('Password updated successfully');
        reset()
    };

    const newPassword = watch('newPassword');
    return (
        <>
            <form onSubmit={handleSubmit(onSubmit)}>
                <div className="row gy-30">
                    {/* Current Password */}
                    <div className="col-lg-12">
                        <div className="form-group">
                            <input
                                {...register('currentPassword', { required: 'Current password is required' })}
                                type="password"
                                placeholder="Current Password"
                            />
                            <ErrorMsg error={errors?.currentPassword?.message} />
                        </div>
                    </div>

                    {/* New Password */}
                    <div className="col-lg-12">
                        <div className="form-group">
                            <input
                                {...register('newPassword', {
                                    required: 'New password is required',
                                    minLength: {
                                        value: 8,
                                        message: 'Password must be at least 8 characters long',
                                    },
                                    pattern: {
                                        value: /^(?=.*[0-9])(?=.*[!@#$%^&*])/,
                                        message: 'Password must include at least one number and one special character',
                                    },
                                })}
                                type="password"
                                placeholder="New Password"
                            />
                            <ErrorMsg error={errors?.newPassword?.message} />
                        </div>
                    </div>

                    {/* Confirm Password */}
                    <div className="col-lg-12">
                        <div className="form-group">
                            <input
                                {...register('confirmPassword', {
                                    required: 'Confirm password is required',
                                    validate: value => value === newPassword || 'Passwords do not match',
                                })}
                                type="password"
                                placeholder="Confirm Password"
                            />
                            <ErrorMsg error={errors?.confirmPassword?.message} />
                        </div>
                    </div>

                    {/* Submit Button */}
                    <div className="col-lg-12">
                        <button type="submit" className="bd-btn btn-primary">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </>
    );
};

export default InstructorChangePassForm;