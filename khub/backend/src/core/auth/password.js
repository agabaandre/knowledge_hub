import bcrypt from 'bcryptjs';

const ROUNDS = 12;

export const hashPassword = (plain) => bcrypt.hash(plain, ROUNDS);

/**
 * Laravel writes bcrypt hashes with the `$2y$` prefix; bcryptjs only
 * understands `$2a$`/`$2b$`. The algorithms are identical, so rewriting the
 * prefix lets migrated users keep their passwords.
 */
export const verifyPassword = async (plain, hash) => {
  if (!hash) return false;
  const compatible = hash.startsWith('$2y$') ? `$2a$${hash.slice(4)}` : hash;
  return bcrypt.compare(plain, compatible);
};
