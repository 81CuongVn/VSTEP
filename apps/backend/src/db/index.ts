import { env } from "@common/env";
import { sql } from "drizzle-orm";
import { drizzle } from "drizzle-orm/bun-sql";
import * as relations from "./relations";
import { table } from "./schema";

export const db = drizzle(env.DATABASE_URL, {
  schema: { ...table, ...relations },
});

await db.execute(sql`SET timezone = 'Asia/Ho_Chi_Minh'`);

export { table };
export { omitColumns, paginate, takeFirst, takeFirstOrThrow } from "./helpers";

export type DbTransaction = Parameters<Parameters<typeof db.transaction>[0]>[0];
export type { UserProgress } from "./schema";
