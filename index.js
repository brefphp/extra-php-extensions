'use strict';

/**
 * This file declares a plugin for the Serverless framework.
 *
 * This lets us define variables and helpers to simplify creating PHP applications.
 */

class ServerlessPlugin {
    constructor(serverless, options) {
        const fs = require("fs");
        const path = require('path');
        const filename = path.resolve(__dirname, 'layers.json');
        const layers = JSON.parse(fs.readFileSync(filename));

        // Declare `${bref-extra:xxx}` variables
        // See https://www.serverless.com/framework/docs/providers/aws/guide/plugins#custom-variable-types
        this.configurationVariablesSources = {
            'bref-extra': {
                async resolve({address, params, resolveConfigurationProperty, options}) {
                    // `address` and `params` reflect values configured with a variable: ${bref-extra(param1, param2):address}

                    // `options` is CLI options
                    // `resolveConfigurationProperty` allows to access other configuration properties,
                    // and guarantees to return a fully resolved form (even if property is configured with variables)
                    const region = options.region || await resolveConfigurationProperty(['provider', 'region']);

                    if (! (address in layers)) {
                        throw new Error(`Unknown Bref extra layer named "${address}"`);
                    }
                    if (! (region in layers[address])) {
                        throw new Error(`There is no Bref extra layer named "${address}" in region "${region}"`);
                    }
                    const version = layers[address][region];
                    return {
                        value: `arn:aws:lambda:${region}:403367587399:layer:${address}:${version}`,
                    }
                }
            }
        };

        // This is the legacy way of declaring `${bref-extra:xxx}` variables. This has been deprecated in 20210326.
        // Override the variable resolver to declare our own variables
        const delegate = serverless.variables
            .getValueFromSource.bind(serverless.variables);
        serverless.variables.getValueFromSource = (variableString) => {
            if (variableString.startsWith('bref-extra:')) {
                const region = serverless.getProvider('aws').getRegion();
                const layerName = variableString.substr('bref-extra:'.length);
                if (! (layerName in layers)) {
                    throw `Unknown Bref extra layer named "${layerName}"`;
                }
                if (! (region in layers[layerName])) {
                    throw `There is no Bref extra layer named "${layerName}" in region "${region}"`;
                }
                const version = layers[layerName][region];
                return `arn:aws:lambda:${region}:403367587399:layer:${layerName}:${version}`;
            }

            return delegate(variableString);
        }
    }
}

module.exports = ServerlessPlugin;

