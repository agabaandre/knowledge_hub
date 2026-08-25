import React from 'react';

const Typography = () => {
    return (
        <>
            <div className="bd-style-guide-typography section-space-small-bottom">
                <h5 className="bd-style-guide-title mb-20">Typography</h5>
                <div className="bd-style-typography-font mb-30">
                    <ul className="bd-style-typography-font-list">
                        <li><span className="subtitle">Body and Title Font:</span> <span>Roboto</span>
                        </li>
                        <li className="font-two"><span className="subtitle">Courses Card Title Font:</span>
                            <span>Big Shoulder Display</span>
                        </li>
                    </ul>
                </div>
                <ul className="bd-style-typography-list">
                    <li>
                        <h1>H1. This is a Main Heading</h1>
                    </li>
                    <li>
                        <h2>H2. Subheading for Sections</h2>
                    </li>
                    <li>
                        <h3>H3. Supporting Heading</h3>
                    </li>
                    <li>
                        <h4>H4. Informational Heading</h4>
                    </li>
                    <li>
                        <h5>H5. Minor Heading</h5>
                    </li>
                    <li>
                        <h6>H6. Smallest Heading</h6>
                    </li>
                    <li>
                        <p>Body Text: Typography plays a crucial role in creating visually appealing
                            and
                            readable designs.</p>
                    </li>
                    <li>
                        <p className="b4">B4- Secondary body text, slightly smaller font size than
                            default.
                        </p>
                    </li>
                    <li>
                        <p className="b3">B3- Body text used for captions or annotations.</p>
                    </li>
                    <li>
                        <p className="b2">B2- Alternative text style for descriptions.</p>
                    </li>
                    <li>
                        <p className="b1">B1- Smallest body text for fine details or footnotes.</p>
                    </li>
                </ul>
            </div>
        </>
    );
};

export default Typography;