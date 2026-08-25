import React from 'react';
import successfullyImg from '../../public/assets/images/joining/successfully.webp';
import Image from 'next/image';

const JoiningForm = ({ activeStep }: { activeStep: string }) => {
    return (
        <>

            <form action="#">
                {/* -- First Step -- */}
                {activeStep === "formStepOne" && (
                    <div className="row g-30 bd-form-setup-content" id="formStepOne">
                        <div className="col-md-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="firstName">First Name<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="firstName" id="firstName" type="text" placeholder="First Name" />
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="lastName">Last Name<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="lastName" id="lastName" type="text" placeholder="Last Name" />
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="emailAddress">Email Address<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="email" id="emailAddress" type="email" placeholder="Email Address" />
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="userName">Username<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="username" id="userName" type="text" placeholder="Username" autoComplete="Username"/>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="password">Password<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="password" id="password" type="password" placeholder="Password" autoComplete="Password"/>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="passwordConfirmation">Confirm Password<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <input name="password_confirmation" id="passwordConfirmation" type="password" placeholder="Confirm Password" autoComplete="new-password" />
                                </div>
                            </div>
                        </div>
                        <div className="col-md-12">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="biography">Biography<span>*</span></label>
                                </div>
                                <div className="form-input">
                                    <textarea id="biography" rows={2} placeholder="Biography"></textarea>
                                </div>
                            </div>
                        </div>
                        <div className="mt-30">
                            <button className="nextBtn bd-btn btn-primary" type="button">Next</button>
                        </div>
                    </div>
                )}
                {/* -- Second Step -- */}
                {activeStep === "formStepTwo" && (
                    <div className="bd-form-setup-content" id="formStepTwo">
                        <div className="row gy-30 mb-30">
                            <h4 className="bd-acc-title">Institution 01</h4>
                            <div className="col-md-6">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="institutionName" data-error="wrong" data-success="right">Institution Name<span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="institutionName" type="text" placeholder="Institution Name" />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="passingYear" data-error="wrong" data-success="right">Passing Year <span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="passingYear" type="text" placeholder="Passing Year" />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="gpa" data-error="wrong" data-success="right">GPA<span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="gpa" type="text" placeholder="GPA" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="row gy-30 mb-30">
                            <h4 className="bd-acc-title">Institution 02</h4>
                            <div className="col-md-6">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="institutionNameTwo" data-error="wrong" data-success="right">Institution Name<span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="institutionNameTwo" type="text" placeholder="Institution Name" />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="passingYearTwo" data-error="wrong" data-success="right">Passing Year <span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="passingYearTwo" type="text" placeholder="Passing Year" />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="cgpa" data-error="wrong" data-success="right">CGPA<span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="cgpa" type="text" placeholder="CGPA" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="row gy-30 mb-30">
                            <h4 className="bd-acc-title">Institution 03</h4>
                            <div className="col-md-6">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="institutionNameThree" data-error="wrong" data-success="right">Institution Name<span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="institutionNameThree" type="text" placeholder="Institution Name" />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="passingYearThree" data-error="wrong" data-success="right">Passing Year <span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="passingYearThree" type="text" placeholder="Passing Year" />
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="form-input-box">
                                    <div className="form-input-title">
                                        <label htmlFor="cgpaTwo" data-error="wrong" data-success="right">CGPA<span>*</span></label>
                                    </div>
                                    <div className="form-input">
                                        <input name="name" id="cgpaTwo" type="text" placeholder="CGPA" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="d-flex-between mt-30">
                            <button className="prevBtn bd-btn btn-secondary" type="button">Previous</button>
                            <button className="nextBtn bd-btn btn-primary" type="button">Submit</button>
                        </div>
                    </div>
                )}

                {/* -- Third Step -- */}
                {activeStep === "formStepThree" && (
                    <div className="row bd-form-setup-content justify-content-center" id="formStepThree">
                        <div className="col-md-8">
                            <div className="text-center">
                                <div className="successfully-thumb mb-20"><Image src={successfullyImg} alt="image" /></div>
                                <h3 className="mb-15">Registration Completed Successfully!</h3>
                                <p>Thank you for becoming an instructor. We will review your
                                    application and get back to you soon.</p>
                            </div>
                        </div>
                    </div>
                )}
            </form>
        </>
    );
};

export default JoiningForm;