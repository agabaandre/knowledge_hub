"use client";

export default function KhSection({
  title,
  children,
}: {
  title: string;
  children: React.ReactNode;
}) {
  return (
    <section className="bd-blog-area section-space">
      <div className="container">
        <div className="row">
          <div className="col-12">
            <div className="bd-section-title-wrapper mb-30">
              <h2 className="bd-section-title">{title}</h2>
            </div>
          </div>
        </div>
        <div className="row gy-30">{children}</div>
      </div>
    </section>
  );
}
