import Link from "next/link";
import ErrorBgImg from "../../../../../public/assets/images/bg/error-bg.webp";

export default function Custom404() {
    return (
        // -- error content area start --
        <section className="bd-error-area bd-error-bg d-flex align-items-center justify-content-center fix" style={{ backgroundImage: `url(${ErrorBgImg.src})` }}>
            <div className="container">
                <div className="row justify-content-center align-items-center">
                    <div className="col-xxl-6 col-xl-8 col-lg-6 col-md-8">
                        <div className="bd-error-wrapper text-center">
                            <h1 className="error-heading-title">404</h1>
                            <h2 className="bd-section-title white-text mb-20">Oops! Page Not Found</h2>
                            <p className="bd-section-paragraph">{`We're`} really sorry but we {`can't`} seem to find the page you
                                were looking for.</p>
                            <div className="error-btn">
                                <Link href="/" className="bd-btn btn-primary">Back To Home</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        // -- error content area end --
    )
}