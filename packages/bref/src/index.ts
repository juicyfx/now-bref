import path from 'path';
import {
  createLambda,
  rename,
  shouldServe,
  BuildOptions,
  FileFsRef
} from '@now/build-utils';
import { getBrefFiles } from '@now-bref/lib';
import { getIncludedFiles } from './utils';

// ###########################
// EXPORTS
// ###########################

export async function build({
  files,
  entrypoint,
  workPath,
  config = {},
  meta = {},
}: BuildOptions) {

  // Collect included files
  const includedFiles = await getIncludedFiles({ files, entrypoint, workPath, config, meta });

  // Move all user files to LAMBDA_ROOT/user folder.
  const userFiles = rename(includedFiles, name => path.join('user', name));

  const bridgeFiles: Files = {
    ...await getBrefFiles(),
    ...{
      'bootstrap': new FileFsRef({
        mode: 0o755,
        fsPath: path.join(__dirname, '../lib/bootstrap'),
      }),
    }
  };

  if (process.env.NOW_PHP_DEBUG === '1') {
    console.log('🐘 Entrypoint:', entrypoint);
    console.log('🐘 Config:', config);
    console.log('🐘 Work path:', workPath);
    console.log('🐘 Meta:', meta);
    console.log('🐘 User files:', Object.keys(userFiles));
    console.log('🐘 Bridge files:', Object.keys(bridgeFiles));
  }

  const lambda = await createLambda({
    files: {
      ...userFiles,
      ...bridgeFiles
    },
    handler: entrypoint,
    runtime: 'provided',
    environment: {
      PHP_INI_SCAN_DIR: '/var/task/native/bref/etc/php/conf.d'
    },
  });

  return { [entrypoint]: lambda };
};

export { shouldServe };
