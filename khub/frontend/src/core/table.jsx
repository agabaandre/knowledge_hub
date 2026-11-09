import { useMemo, useState } from 'react';
import { formatNumber } from './format.js';
import { EmptyState } from './ui.jsx';

/**
 * DataTable + TablePagination — ported from the MoH performance management
 * system's organisms/DataTable.tsx and molecules/TablePagination.tsx
 * (zebra rows, uppercase subtle header, record-count pill, numbered pager
 * with ellipsis), restyled to the hub's tokens in styles/admin.css.
 *
 * Columns: [{ key, label, render?(row, index), align?, width?, nowrap? }]
 * Server mode: pass rows for the current page + { page, perPage, totalPages, total, onPageChange };
 * row numbers continue across pages (page 2 of 15 starts at 16).
 * Client mode: pass all rows + clientPerPage.
 */
export const DataTable = ({
  title,
  description,
  columns,
  rows,
  rowKey = (row) => row.id,
  showRowNumbers = false,
  emptyMessage = 'No records found.',
  emptyIcon = 'fa-database',
  toolbar,
  clientPerPage,
  page: serverPage,
  perPage: serverPerPage,
  totalPages: serverTotalPages,
  total: serverTotal,
  onPageChange,
}) => {
  const [clientPage, setClientPage] = useState(1);
  const serverMode = typeof onPageChange === 'function';

  const page = serverMode ? serverPage : clientPage;
  const perPage = serverMode ? (serverPerPage ?? rows.length ?? 1) : (clientPerPage ?? (rows.length || 1));
  const total = serverMode ? serverTotal : rows.length;
  const totalPages = serverMode ? serverTotalPages : Math.max(1, Math.ceil(rows.length / perPage));
  const pageRows = useMemo(() => {
    if (serverMode || !clientPerPage) return rows;
    const safePage = Math.min(clientPage, Math.max(1, Math.ceil(rows.length / clientPerPage)));
    return rows.slice((safePage - 1) * clientPerPage, safePage * clientPerPage);
  }, [rows, serverMode, clientPerPage, clientPage]);
  const offset = (page - 1) * perPage;
  const setPage = serverMode ? onPageChange : setClientPage;

  return (
    <div className="kh-table-card">
      {(title || toolbar) && (
        <div className="kh-table-head">
          <div>
            {title && <h2>{title}</h2>}
            {description && <p>{description}</p>}
          </div>
          <div className="kh-table-head-end">
            {toolbar}
            <span className="kh-table-pill">
              <i className="fa fa-database" aria-hidden="true" />
              <strong>{formatNumber(total)}</strong> record{total === 1 ? '' : 's'}
            </span>
          </div>
        </div>
      )}
      <div className="kh-table-scroll">
        <table className="kh-table">
          <thead>
            <tr>
              {showRowNumbers && <th className="kh-table-num">#</th>}
              {columns.map((column) => (
                <th key={column.key} style={{ width: column.width, textAlign: column.align }}>{column.label}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {pageRows.length === 0 && (
              <tr>
                <td colSpan={columns.length + (showRowNumbers ? 1 : 0)}>
                  <EmptyState icon={emptyIcon} title={emptyMessage} />
                </td>
              </tr>
            )}
            {pageRows.map((row, index) => (
              <tr key={rowKey(row, index)}>
                {showRowNumbers && <td className="kh-table-num">{offset + index + 1}</td>}
                {columns.map((column) => (
                  <td key={column.key} style={{ textAlign: column.align, whiteSpace: column.nowrap ? 'nowrap' : undefined }}>
                    {column.render ? column.render(row, index) : row[column.key]}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <TablePagination
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        summary={total === 0 ? 'No records' : `${formatNumber(offset + 1)}–${formatNumber(Math.min(offset + pageRows.length, total))} of ${formatNumber(total)}`}
      />
    </div>
  );
};

/** First/last pages always visible, a window around the current page, ellipsis between. */
const buildPageList = (page, totalPages) => {
  if (totalPages <= 7) return Array.from({ length: totalPages }, (_, index) => index + 1);
  const pages = new Set([1, totalPages, page - 1, page, page + 1]);
  const sorted = [...pages].filter((value) => value >= 1 && value <= totalPages).sort((a, b) => a - b);
  const tokens = [];
  sorted.forEach((value, index) => {
    if (index > 0 && value - sorted[index - 1] > 1) tokens.push('ellipsis');
    tokens.push(value);
  });
  return tokens;
};

export const TablePagination = ({ page, totalPages, onPageChange, summary }) => {
  if (totalPages <= 1 && !summary) return null;
  return (
    <div className="kh-table-foot">
      <p>{summary}</p>
      {totalPages > 1 && (
        <nav className="kh-table-pager" aria-label="Pagination">
          <button type="button" disabled={page <= 1} onClick={() => onPageChange(page - 1)} aria-label="Previous page">
            <i className="fa fa-chevron-left" aria-hidden="true" />
          </button>
          {buildPageList(page, totalPages).map((token, index) =>
            token === 'ellipsis' ? (
              <span key={`e-${index}`} aria-hidden="true">…</span>
            ) : (
              <button key={token} type="button" className={token === page ? 'is-current' : ''}
                aria-current={token === page ? 'page' : undefined} onClick={() => onPageChange(token)}>
                {token}
              </button>
            ),
          )}
          <button type="button" disabled={page >= totalPages} onClick={() => onPageChange(page + 1)} aria-label="Next page">
            <i className="fa fa-chevron-right" aria-hidden="true" />
          </button>
        </nav>
      )}
    </div>
  );
};
