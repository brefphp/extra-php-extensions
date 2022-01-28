'use strict';

/**
 * This file declares a plugin for the Serverless framework.
 *
 * This lets us define variables and helpers to simplify creating PHP applications.
 */

class ServerlessPlugin {
    constructor(serverless, options, utils) {
        this.serverless = serverless;
        this.options = options;
        this.utils = utils;
        this.provider = this.serverless.getProvider('aws');

        this.fs = require('fs');
        this.path = require('path');

        const filename = this.path.resolve(__dirname, 'layers.json');
        const layers = JSON.parse(this.fs.readFileSync(filename));

        // Declare `${bref-extra:xxx}` variables
        // See https://www.serverless.com/framework/docs/guides/plugins/custom-variables
        this.configurationVariablesSources = {
            'bref-extra': {
                async resolve({address, params, resolveConfigurationProperty, options}) {
                    // `address` and `params` reflect values configured with a variable: ${bref-extra(param1, param2):address}

                    // `options` is CLI options
                    // `resolveConfigurationProperty` allows to access other configuration properties,
                    // and guarantees to return a fully resolved form (even if property is configured with variables)
                    const region = options.region || await resolveConfigurationProperty(['provider', 'region']);

                    if (! (address in layers)) {
                        throw new this.serverless.classes.Error(`Unknown Bref extra layer named "${address}"`);
                    }
                    if (! (region in layers[address])) {
                        throw new this.serverless.classes.Error(`There is no Bref extra layer named "${address}" in region "${region}"`);
                    }
                    const version = layers[address][region];
                    return {
                        value: `arn:aws:lambda:${region}:403367587399:layer:${address}:${version}`,
                    }
                }
            }
        };

        // If we are on Serverless Framework v2, set up the legacy variable resolver
        if (!this.utils) {
            // This is the legacy way of declaring `${bref-extra:xxx}` variables. This has been deprecated in 20210326.
            // Override the variable resolver to declare our own variables
            const delegate = this.serverless.variables
                .getValueFromSource.bind(this.serverless.variables);
            this.serverless.variables.getValueFromSource = (variableString) => {
                if (variableString.startsWith('bref-extra:')) {
                    const region = this.provider.getRegion();
                    const layerName = variableString.substr('bref-extra:'.length);
                    if (!(layerName in layers)) {
                        throw new serverless.classes.Error(`Unknown Bref extra layer named "${layerName}".`);
                    }
                    if (!(region in layers[layerName])) {
                        throw new serverless.classes.Error(`There is no Bref extra layer named "${layerName}" in region "${region}".`);
                    }
                    const version = layers[layerName][region];
                    return `arn:aws:lambda:${region}:403367587399:layer:${layerName}:${version}`;
                }

                return delegate(variableString);
            }
        }
    }
}

module.exports = ServerlessPlugin;

