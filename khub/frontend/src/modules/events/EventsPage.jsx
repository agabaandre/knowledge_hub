import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { formatDate } from '../../core/format.js';
import { useSite } from '../../core/site-context.jsx';
import { Page, Loading } from '../../core/Page.jsx';

export const EventsPage = () => {
  const { manifest } = useSite();
  const { data, error, loading } = useApi('/api/events', { per_page: 24 });

  return (
    <Page title="Events" lead="Upcoming public health events and training.">
      <Seo title="Events" path="/events" description={`Public health events and training on ${manifest.settings.site_name}.`} />
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      <div className="row">
        {data?.data.map((event) => (
          <div className="col-lg-6 col-12 mb-3" key={event.id}>
            <div className="jbr-wrap border rounded bg-white p-3 h-100">
              <h2 className="fs-md ft-medium mb-1" style={{ fontSize: '1.02rem' }}>
                {event.registrationLink || event.eventLink
                  ? <a href={event.registrationLink || event.eventLink} target="_blank" rel="noopener noreferrer">{event.title}</a>
                  : event.title}
              </h2>
              <p className="text-muted medium mb-0">
                <i className="fa fa-calendar mr-1" aria-hidden="true" />
                <time dateTime={event.startDate}>{formatDate(event.startDate)}</time>
                {event.endDate && <> — <time dateTime={event.endDate}>{formatDate(event.endDate)}</time></>}
                {event.venue && <span className="ml-2"><i className="fa fa-location-dot mr-1" aria-hidden="true" />{event.venue}</span>}
                {event.isOnline && <span className="ml-2"><i className="fa fa-video mr-1" aria-hidden="true" />Online</span>}
              </p>
            </div>
          </div>
        ))}
      </div>
      {data && data.data.length === 0 && !loading && <div className="alert alert-light border">No upcoming events.</div>}
    </Page>
  );
};
