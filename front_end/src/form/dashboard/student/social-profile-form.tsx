import React from 'react';

const SocialProfileForm = () => {
    return (
        <>
                <div className="col-lg-12">
                    <div className="bd-social-form-group">
                        <input className="bd-social-input" type="text" name="website-url" placeholder="Website (http(s)://istudy.com)" />
                    </div>
                </div>
                <div className="col-lg-12">
                    <div className="bd-social-form-group">
                        <div className="bd-social-input-with-links">
                            <div className="bd-social-input-container">
                                <div className="text-input-with-addons-links">
                                    http://twitter.com/
                                </div>
                                <input className="bd-social-input" name="twitter-profile" placeholder="Twitter Profile" type="text" />
                                <div className="bd-social-input-box"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-lg-12">
                    <div className="bd-social-form-group">
                        <div className="bd-social-input-with-links">
                            <div className="bd-social-input-container">
                                <div className="text-input-with-addons-links">
                                    http://www.facebook.com/</div>
                                <input className="bd-social-input" name="facebook-profile" placeholder="Twitter Profile" type="text" />
                                <div className="bd-social-input-box"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-lg-12">
                    <div className="bd-social-form-group">
                        <div className="bd-social-input-with-links">
                            <div className="bd-social-input-container">
                                <div className="text-input-with-addons-links">
                                    http://www.linkedin.com/</div>
                                <input className="bd-social-input" name="linkedin-profile" placeholder="Linkedin Profile" type="text" />
                                <div className="bd-social-input-box"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-lg-12">
                    <div className="bd-social-form-group">
                        <div className="bd-social-input-with-links">
                            <div className="bd-social-input-container">
                                <div className="text-input-with-addons-links">
                                    http://www.youtube.com/</div>
                                <input className="bd-social-input" name="youtube-profile" placeholder="Youtube Profile" type="text" />
                                <div className="bd-social-input-box"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-lg-12">
                    <div className="bd-social-form-group">
                        <div className="bd-social-input-with-links">
                            <div className="bd-social-input-container">
                                <div className="text-input-with-addons-links">
                                    http://www.github.com/
                                </div>
                                <input className="bd-social-input" name="github-profile" placeholder="Github Profile" type="text" />
                                <div className="bd-social-input-box"></div>
                            </div>
                        </div>
                    </div>
                </div>
        </>
    );
};

export default SocialProfileForm;  
