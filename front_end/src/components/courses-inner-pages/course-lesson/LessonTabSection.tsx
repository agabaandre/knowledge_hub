import { useEffect, useState } from 'react';
import Link from 'next/link';

const LessonTabSection = () => {
    const [activeTab, setActiveTab] = useState('pills-homeTwo');

    const handleTabChange = (tabId: string) => {
        setActiveTab(tabId);
    };

    useEffect(() => {
        // Ensure window.bootstrap is available
        if (typeof window !== 'undefined' && window.bootstrap) {
            const tabElement = document.querySelector('#pills-tabTwo');
            if (tabElement) {
                const tabs = new window.bootstrap.Tab(tabElement);
                tabs.show();
            }
        }
    }, []);

    return (
        <>
            <div className="tab-style-two">
                <ul className="nav nav-pills" id="pills-tabTwo" role="tablist">
                    <li className="nav-item" role="presentation">
                        <button
                            className={`nav-link ${activeTab === 'pills-homeTwo' ? 'active' : ''}`}
                            id="pills-homeTwo-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-homeTwo"
                            type="button"
                            role="tab"
                            aria-controls="pills-homeTwo"
                            aria-selected={activeTab === 'pills-homeTwo' ? 'true' : 'false'}
                            onClick={() => handleTabChange('pills-homeTwo')}
                        >
                            About Lesson
                        </button>
                    </li>
                    <li className="nav-item" role="presentation">
                        <button
                            className={`nav-link ${activeTab === 'pills-profileTwo' ? 'active' : ''}`}
                            id="pills-profileTwo-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-profileTwo"
                            type="button"
                            role="tab"
                            aria-controls="pills-profileTwo"
                            aria-selected={activeTab === 'pills-profileTwo' ? 'true' : 'false'}
                            onClick={() => handleTabChange('pills-profileTwo')}
                        >
                            Lesson Resource
                        </button>
                    </li>
                </ul>
                <div className="tab-content" id="pills-tabTwoContent">
                    <div
                        className={`tab-pane fade ${activeTab === 'pills-homeTwo' ? 'show active' : ''}`}
                        id="pills-homeTwo"
                        role="tabpanel"
                        aria-labelledby="pills-homeTwo-tab"
                        tabIndex={0}
                    >
                        This is some placeholder content the Home {`tab's`} associated content. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling. You can use it with tabs, pills, and any other .nav-powered navigation.
                    </div>
                    <div
                        className={`tab-pane fade ${activeTab === 'pills-profileTwo' ? 'show active' : ''}`}
                        id="pills-profileTwo"
                        role="tabpanel"
                        aria-labelledby="pills-profileTwo-tab"
                        tabIndex={0}
                    >
                        <Link href="#" className="bd-lesson-materials">
                            <i className="fa-sharp fa-solid fa-file-zip"></i>
                            download-course-materials.zip
                        </Link>
                    </div>
                </div>
            </div>
        </>
    );
};

export default LessonTabSection;
