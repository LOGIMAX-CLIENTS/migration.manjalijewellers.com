/**
 * Plugins Registration
 */
import { PluginRegistry } from '@core/plugin-system';
import { lcaPlugin } from './lca';
import { testingPlugin } from './testing';
import { logsPlugin } from './logs';

PluginRegistry.register(lcaPlugin);
PluginRegistry.register(testingPlugin);
PluginRegistry.register(logsPlugin);

export { lcaPlugin, testingPlugin, logsPlugin };
