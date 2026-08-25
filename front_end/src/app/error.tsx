'use client';

import { useEffect } from "react";
import ErrorBgImg from "../../public/assets/images/bg/error-bg.webp";

export default function GlobalError({
    error,
    reset,
}: {
    error: Error;
    reset: () => void;
}) {
    useEffect(() => {
        console.error("Global error caught:", error);
        // You can also send this to a logger or alerting system here
    }, [error]);

    return (
        <div className="bd-error-area bd-error-bg d-flex align-items-center justify-content-center fix" style={{ backgroundImage: `url(${ErrorBgImg.src})` }}>
            <div className="container">
                <div className="row justify-content-center align-items-center">
                    <div className="col-xxl-6 col-xl-8 col-lg-6 col-md-8">
                        <div className="bd-error-wrapper text-center">
                            <h2 className="bd-section-title white-text mb-20">Something went wrong!</h2>
                            <p className="bd-section-paragraph">{error.message}</p>
                            <div className="error-btn">
                                <button
                                    onClick={() => reset()}
                                    className="bd-btn btn-primary"
                                >
                                    Try again
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
